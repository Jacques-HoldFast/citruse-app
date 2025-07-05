<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity_kg',
        'required_delivery_date',
        'unit_price_ex_vat',
        'total_value_ex_vat',
        'delivered_quantity_kg',
        'actual_delivery_date',
        'quality_status',
        'quality_notes',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:2',
        'unit_price_ex_vat' => 'decimal:2',
        'total_value_ex_vat' => 'decimal:2',
        'delivered_quantity_kg' => 'decimal:2',
        'required_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
    ];

    // Quality status constants
    const QUALITY_PENDING = 'pending';
    const QUALITY_ACCEPTED = 'accepted';
    const QUALITY_REJECTED = 'rejected';

    /**
     * Relationships
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Business Logic Methods
     */
    public function calculateTotalValue(): float
    {
        return $this->quantity_kg * $this->unit_price_ex_vat;
    }

    public function isFullyDelivered(): bool
    {
        return $this->delivered_quantity_kg >= $this->quantity_kg;
    }

    public function isPartiallyDelivered(): bool
    {
        return $this->delivered_quantity_kg > 0 && $this->delivered_quantity_kg < $this->quantity_kg;
    }

    public function getDeliveryStatus(): string
    {
        if (!$this->delivered_quantity_kg) {
            return 'pending';
        }

        if ($this->isFullyDelivered()) {
            return 'delivered';
        }

        return 'partial';
    }

    public function getShortfallQuantity(): float
    {
        return max(0, $this->quantity_kg - ($this->delivered_quantity_kg ?? 0));
    }

    public function isOverdue(): bool
    {
        return $this->required_delivery_date < now() && !$this->isFullyDelivered();
    }

    public function getDaysUntilDue(): int
    {
        return now()->diffInDays($this->required_delivery_date, false);
    }

    /**
     * Automatically calculate total when saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_value_ex_vat = $item->calculateTotalValue();
        });

        static::saved(function ($item) {
            // Update parent PO total
            $item->purchaseOrder->updateTotalValue();
        });

        static::deleted(function ($item) {
            // Update parent PO total
            $item->purchaseOrder->updateTotalValue();
        });
    }
}