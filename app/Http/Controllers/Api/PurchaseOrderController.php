<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders
     * All roles can view, but filtered by permissions
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = PurchaseOrder::with(['supplier', 'distributor', 'createdBy', 'items.product']);

        // Role-based filtering
        if ($user->isFieldSalesAssociate()) {
            // Field Sales can only see orders they need to work with
            $query->whereIn('status', [
                PurchaseOrder::STATUS_NEW,
                PurchaseOrder::STATUS_ACCEPTED_BY_SUPPLIER,
                PurchaseOrder::STATUS_IN_DELIVERY
            ]);
        }

        // Additional filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->has('distributor_id')) {
            $query->where('distributor_id', $request->distributor_id);
        }

        // Date range filtering
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($purchaseOrders);
    }

    /**
     * Store a new purchase order
     * Only Purchasing Managers and System Admins can create orders
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['distributor_order', 'supplier_order'])],
            'supplier_id' => 'nullable|exists:suppliers,id',
            'distributor_id' => 'nullable|exists:distributors,id',
            'linked_po_id' => 'nullable|exists:purchase_orders,id',
            'notes' => 'nullable|string',
            'required_delivery_date' => 'nullable|date|after:today',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_kg' => 'required|numeric|min:0.01',
            'items.*.required_delivery_date' => 'required|date|after:today',
        ]);

        // Business rule validation
        if ($validated['type'] === 'distributor_order' && !$validated['distributor_id']) {
            return response()->json(['message' => 'Distributor is required for distributor orders'], 422);
        }

        if ($validated['type'] === 'supplier_order' && !$validated['supplier_id']) {
            return response()->json(['message' => 'Supplier is required for supplier orders'], 422);
        }

        // Create the purchase order
        $purchaseOrder = PurchaseOrder::create([
            'type' => $validated['type'],
            'supplier_id' => $validated['supplier_id'],
            'distributor_id' => $validated['distributor_id'],
            'linked_po_id' => $validated['linked_po_id'],
            'notes' => $validated['notes'],
            'required_delivery_date' => $validated['required_delivery_date'],
            'created_by' => $request->user()->id,
        ]);

        // Create line items
        foreach ($validated['items'] as $itemData) {
            $product = Product::find($itemData['product_id']);
            $currentPrice = $product->getCurrentPricePerKg();

            if (!$currentPrice) {
                return response()->json([
                    'message' => "No pricing found for product {$product->product_code} for current year"
                ], 422);
            }

            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $itemData['product_id'],
                'quantity_kg' => $itemData['quantity_kg'],
                'required_delivery_date' => $itemData['required_delivery_date'],
                'unit_price_ex_vat' => $currentPrice,
                // total_value_ex_vat is calculated automatically in the model
            ]);
        }

        // Reload with relationships
        $purchaseOrder->load(['supplier', 'distributor', 'createdBy', 'items.product']);

        return response()->json([
            'message' => 'Purchase order created successfully',
            'purchase_order' => $purchaseOrder
        ], 201);
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'supplier',
            'distributor',
            'createdBy',
            'items.product',
            'linkedPO',
            'linkedFromPO'
        ]);

        // Add fulfillment analysis for POD orders
        $data = $purchaseOrder->toArray();
        if ($purchaseOrder->isPOD()) {
            $data['fulfillment_analysis'] = $purchaseOrder->getFulfillmentAnalysis();
            $data['is_fully_committed'] = $purchaseOrder->isFullyCommitted();
            $data['has_shortages'] = $purchaseOrder->hasShortages();
            $data['total_shortage_value'] = $purchaseOrder->getTotalShortageValue();
        }

        return response()->json($data);
    }

    /**
     * Update purchase order status
     * Field Sales Associates can update status, others can update full record
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $user = $request->user();

        if ($user->isFieldSalesAssociate()) {
            // Field Sales can only update status and delivery information
            $validated = $request->validate([
                'status' => [
                    'required',
                    Rule::in([
                        PurchaseOrder::STATUS_ACCEPTED_BY_SUPPLIER,
                        PurchaseOrder::STATUS_IN_DELIVERY,
                        PurchaseOrder::STATUS_DELIVERED,
                        PurchaseOrder::STATUS_REJECTED_BY_SUPPLIER
                    ])
                ],
            ]);

            $purchaseOrder->update(['status' => $validated['status']]);

        } else {
            // Purchasing Managers and System Admins can update more fields
            $validated = $request->validate([
                'status' => [
                    'nullable',
                    Rule::in([
                        PurchaseOrder::STATUS_NEW,
                        PurchaseOrder::STATUS_ACCEPTED_BY_SUPPLIER,
                        PurchaseOrder::STATUS_IN_DELIVERY,
                        PurchaseOrder::STATUS_DELIVERED,
                        PurchaseOrder::STATUS_REJECTED_BY_SUPPLIER,
                        PurchaseOrder::STATUS_REJECTED_BY_DISTRIBUTOR,
                        PurchaseOrder::STATUS_CANCELLED
                    ])
                ],
                'notes' => 'nullable|string',
                'required_delivery_date' => 'nullable|date',
            ]);

            $purchaseOrder->update($validated);
        }

        $purchaseOrder->load(['supplier', 'distributor', 'createdBy', 'items.product']);

        return response()->json([
            'message' => 'Purchase order updated successfully',
            'purchase_order' => $purchaseOrder
        ]);
    }

    /**
     * Update line item delivery details
     * Only Field Sales Associates and System Admins
     */
    public function updateLineItem(Request $request, PurchaseOrderItem $item)
    {
        $validated = $request->validate([
            'delivered_quantity_kg' => 'nullable|numeric|min:0',
            'actual_delivery_date' => 'nullable|date',
            'quality_status' => ['nullable', Rule::in(['pending', 'accepted', 'rejected'])],
            'quality_notes' => 'nullable|string',
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Line item updated successfully',
            'item' => $item->load('product')
        ]);
    }

    /**
     * Get pipeline forecast report
     * Shows all orders due in next 6 months with fulfillment analysis
     */
    public function pipelineForecast(Request $request)
    {
        $months = $request->input('months', 6);

        $distributorOrders = PurchaseOrder::distributorOrders()
            ->with(['distributor', 'items.product'])
            ->whereBetween('required_delivery_date', [now(), now()->addMonths($months)])
            ->whereIn('status', [
                PurchaseOrder::STATUS_NEW,
                PurchaseOrder::STATUS_ACCEPTED_BY_SUPPLIER,
                PurchaseOrder::STATUS_IN_DELIVERY
            ])
            ->orderBy('required_delivery_date')
            ->get();

        $supplierOrders = PurchaseOrder::supplierOrders()
            ->with(['supplier', 'items.product'])
            ->whereBetween('required_delivery_date', [now(), now()->addMonths($months)])
            ->whereIn('status', [
                PurchaseOrder::STATUS_NEW,
                PurchaseOrder::STATUS_ACCEPTED_BY_SUPPLIER,
                PurchaseOrder::STATUS_IN_DELIVERY
            ])
            ->orderBy('required_delivery_date')
            ->get();

        // Add fulfillment analysis to POD orders
        $distributorOrdersWithAnalysis = $distributorOrders->map(function ($pod) {
            $data = $pod->toArray();
            $data['fulfillment_analysis'] = $pod->getFulfillmentAnalysis();
            $data['is_fully_committed'] = $pod->isFullyCommitted();
            $data['has_shortages'] = $pod->hasShortages();
            return $data;
        });

        return response()->json([
            'distributor_orders' => $distributorOrdersWithAnalysis,
            'supplier_orders' => $supplierOrders,
            'summary' => [
                'total_pod_orders' => $distributorOrders->count(),
                'total_pos_orders' => $supplierOrders->count(),
                'orders_with_shortages' => $distributorOrdersWithAnalysis->where('has_shortages', true)->count(),
                'total_pod_value' => $distributorOrders->sum('total_value_ex_vat'),
                'total_pos_value' => $supplierOrders->sum('total_value_ex_vat'),
            ]
        ]);
    }

    /**
     * Delete a purchase order
     * Only System Admins can delete, and only if status allows
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeCancelled()) {
            return response()->json([
                'message' => 'Cannot delete purchase order in current status: ' . $purchaseOrder->status
            ], 422);
        }

        $purchaseOrder->delete();

        return response()->json(['message' => 'Purchase order deleted successfully']);
    }
}