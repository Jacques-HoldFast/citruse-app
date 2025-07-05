<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'type',
        'supplier_id',
        'distributor_id',
        'status',
        'linked_po_id',
        'created_by',
        'notes',
        'required_delivery_date',
        'total_value_ex_vat',
    ];

    protected $casts = [
        'required_delivery_date' => 'date',
        'total_value_ex_vat' => 'decimal:2',
    ];

    // Type constants
    const TYPE_DISTRIBUTOR_ORDER = 'distributor_order';
    const TYPE_SUPPLIER_ORDER = 'supplier_order';

    // Status constants
    const STATUS_NEW = 'new';
    const STATUS_ACCEPTED_BY_SUPPLIER = 'accepted_by_supplier';
    const STATUS_IN_DELIVERY = 'in_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_REJECTED_BY_SUPPLIER = 'rejected_by_supplier';
    const STATUS_REJECTED_BY_DISTRIBUTOR = 'rejected_by_distributor';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relationships
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function linkedPO(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'linked_po_id');
    }

    public function linkedFromPO(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'linked_po_id');
    }

    /**
     * Scopes
     */
    public function scopeDistributorOrders($query)
    {
        return $query->where('type', self::TYPE_DISTRIBUTOR_ORDER);
    }

    public function scopeSupplierOrders($query)
    {
        return $query->where('type', self::TYPE_SUPPLIER_ORDER);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDueInNext6Months($query)
    {
        return $query->whereBetween('required_delivery_date', [
            now(),
            now()->addMonths(6)
        ]);
    }

    /**
     * Business Logic Methods
     */
    public function isPOD(): bool
    {
        return $this->type === self::TYPE_DISTRIBUTOR_ORDER;
    }

    public function isPOS(): bool
    {
        return $this->type === self::TYPE_SUPPLIER_ORDER;
    }

    public function canBeAccepted(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
            self::STATUS_REJECTED_BY_SUPPLIER,
            self::STATUS_REJECTED_BY_DISTRIBUTOR
        ]);
    }

    public function isActive(): bool
    {
        return !in_array($this->status, [
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
            self::STATUS_REJECTED_BY_SUPPLIER,
            self::STATUS_REJECTED_BY_DISTRIBUTOR
        ]);
    }

    /**
     * Generate PO number based on type
     */
    public static function generatePONumber($type): string
    {
        $prefix = $type === self::TYPE_DISTRIBUTOR_ORDER ? 'POD-' : 'POS-';
        $lastPO = static::where('po_number', 'like', $prefix . '%')
            ->orderBy('po_number', 'desc')
            ->first();

        if (!$lastPO) {
            return $prefix . '00001';
        }

        $lastNumber = (int) substr($lastPO->po_number, strlen($prefix));
        return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total value from line items
     */
    public function calculateTotalValue(): float
    {
        return $this->items->sum('total_value_ex_vat');
    }

    /**
     * Update total value and save
     */
    public function updateTotalValue(): void
    {
        $this->total_value_ex_vat = $this->calculateTotalValue();
        $this->save();
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'New',
            self::STATUS_ACCEPTED_BY_SUPPLIER => 'Accepted by Supplier',
            self::STATUS_IN_DELIVERY => 'In Delivery',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_REJECTED_BY_SUPPLIER => 'Rejected by Supplier',
            self::STATUS_REJECTED_BY_DISTRIBUTOR => 'Rejected by Distributor',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    /**
     * Get all POS orders linked to this POD
     */
    public function getLinkedSupplierOrders()
    {
        if ($this->isPOD()) {
            return static::where('linked_po_id', $this->id)
                ->where('type', self::TYPE_SUPPLIER_ORDER)
                ->get();
        }
        return collect();
    }

    /**
     * Calculate fulfillment status for POD orders
     */
    public function getFulfillmentAnalysis()
    {
        if (!$this->isPOD()) {
            return null;
        }

        $analysis = [];

        foreach ($this->items as $podItem) {
            $requestedQty = $podItem->quantity_kg;

            // Get all linked POS items for this product
            $linkedPosItems = PurchaseOrderItem::whereHas('purchaseOrder', function ($q) {
                $q->where('linked_po_id', $this->id)
                    ->where('type', self::TYPE_SUPPLIER_ORDER);
            })->where('product_id', $podItem->product_id)->get();

            $committedQty = $linkedPosItems->sum('quantity_kg');
            $deliveredQty = $linkedPosItems->sum('delivered_quantity_kg');

            $analysis[] = [
                'product_id' => $podItem->product_id,
                'product_code' => $podItem->product->product_code,
                'product_description' => $podItem->product->description,
                'requested_quantity' => $requestedQty,
                'committed_quantity' => $committedQty,
                'delivered_quantity' => $deliveredQty ?? 0,
                'shortage_quantity' => max(0, $requestedQty - $committedQty),
                'pending_delivery' => max(0, $committedQty - ($deliveredQty ?? 0)),
                'fulfillment_percentage' => $requestedQty > 0 ? ($committedQty / $requestedQty) * 100 : 0,
                'delivery_percentage' => $committedQty > 0 ? (($deliveredQty ?? 0) / $committedQty) * 100 : 0,
            ];
        }

        return $analysis;
    }

    /**
     * Check if POD is fully committed by suppliers
     */
    public function isFullyCommitted(): bool
    {
        if (!$this->isPOD()) {
            return false;
        }

        $analysis = $this->getFulfillmentAnalysis();
        return collect($analysis)->every(fn($item) => $item['shortage_quantity'] == 0);
    }

    /**
     * Check if POD has any shortages
     */
    public function hasShortages(): bool
    {
        if (!$this->isPOD()) {
            return false;
        }

        $analysis = $this->getFulfillmentAnalysis();
        return collect($analysis)->some(fn($item) => $item['shortage_quantity'] > 0);
    }

    /**
     * Get total shortage across all products
     */
    public function getTotalShortageValue(): float
    {
        if (!$this->isPOD()) {
            return 0;
        }

        $totalShortage = 0;
        foreach ($this->getFulfillmentAnalysis() as $item) {
            $productPrice = $this->items()
                ->where('product_id', $item['product_id'])
                ->first()->unit_price_ex_vat ?? 0;
            $totalShortage += $item['shortage_quantity'] * $productPrice;
        }

        return $totalShortage;
    }

    /**
     * Boot method to auto-generate PO numbers
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseOrder) {
            if (!$purchaseOrder->po_number) {
                $purchaseOrder->po_number = static::generatePONumber($purchaseOrder->type);
            }
        });
    }
}