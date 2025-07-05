<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     * All authenticated users can view suppliers
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'ILIKE', "%{$search}%")
                    ->orWhere('country', 'ILIKE', "%{$search}%")
                    ->orWhere('primary_sales_contact_name', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->has('country')) {
            $query->byCountry($request->country);
        }

        // Include purchase order counts for System Admins and Purchasing Managers
        $user = $request->user();
        if ($user->canManageSuppliers()) {
            $query->withCount(['purchaseOrders', 'activePurchaseOrders']);
        }

        $suppliers = $query->orderBy('business_name')->paginate(15);

        return response()->json($suppliers);
    }

    /**
     * Store a newly created supplier
     * Only System Admins and Purchasing Managers can create suppliers
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'registered_address' => 'required|string',
            'country' => 'required|string|max:100',
            'vat_number' => 'required|string|max:50|unique:suppliers,vat_number',
            'primary_sales_contact_name' => 'required|string|max:255',
            'primary_sales_contact_email' => 'required|email|max:255',
            'primary_sales_contact_telephone' => 'required|string|max:50',
            'primary_logistics_contact_name' => 'required|string|max:255',
            'primary_logistics_contact_email' => 'required|email|max:255',
            'primary_logistics_contact_telephone' => 'required|string|max:50',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'message' => 'Supplier created successfully',
            'supplier' => $supplier
        ], 201);
    }

    /**
     * Display the specified supplier
     */
    public function show(Supplier $supplier, Request $request)
    {
        $user = $request->user();

        // Load purchase orders for authorized users
        if ($user->canManageSuppliers()) {
            $supplier->load([
                'purchaseOrders' => function ($query) {
                    $query->with(['items.product'])->orderBy('created_at', 'desc')->limit(10);
                }
            ]);
        }

        return response()->json($supplier);
    }

    /**
     * Update the specified supplier
     * Only System Admins and Purchasing Managers can update suppliers
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'business_name' => 'sometimes|required|string|max:255',
            'registered_address' => 'sometimes|required|string',
            'country' => 'sometimes|required|string|max:100',
            'vat_number' => 'sometimes|required|string|max:50|unique:suppliers,vat_number,' . $supplier->id,
            'primary_sales_contact_name' => 'sometimes|required|string|max:255',
            'primary_sales_contact_email' => 'sometimes|required|email|max:255',
            'primary_sales_contact_telephone' => 'sometimes|required|string|max:50',
            'primary_logistics_contact_name' => 'sometimes|required|string|max:255',
            'primary_logistics_contact_email' => 'sometimes|required|email|max:255',
            'primary_logistics_contact_telephone' => 'sometimes|required|string|max:50',
        ]);

        $supplier->update($validated);

        return response()->json([
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier
        ]);
    }

    /**
     * Remove the specified supplier
     * Only System Admins can delete suppliers
     */
    public function destroy(Supplier $supplier)
    {
        // Check if supplier has any purchase orders
        if ($supplier->purchaseOrders()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete supplier with existing purchase orders'
            ], 422);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully']);
    }

    /**
     * Get supplier performance metrics
     * Only System Admins and Purchasing Managers
     */
    public function performance(Supplier $supplier)
    {
        $purchaseOrders = $supplier->purchaseOrders()
            ->with('items')
            ->where('type', 'supplier_order')
            ->get();

        $totalOrders = $purchaseOrders->count();
        $deliveredOrders = $purchaseOrders->where('status', 'delivered')->count();
        $rejectedOrders = $purchaseOrders->where('status', 'rejected_by_distributor')->count();
        $onTimeDeliveries = $purchaseOrders->filter(function ($po) {
            return $po->items->every(function ($item) {
                return $item->actual_delivery_date &&
                    $item->actual_delivery_date <= $item->required_delivery_date;
            });
        })->count();

        return response()->json([
            'supplier' => $supplier->business_name,
            'metrics' => [
                'total_orders' => $totalOrders,
                'delivered_orders' => $deliveredOrders,
                'rejected_orders' => $rejectedOrders,
                'on_time_deliveries' => $onTimeDeliveries,
                'delivery_rate' => $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0,
                'rejection_rate' => $totalOrders > 0 ? ($rejectedOrders / $totalOrders) * 100 : 0,
                'on_time_rate' => $totalOrders > 0 ? ($onTimeDeliveries / $totalOrders) * 100 : 0,
            ]
        ]);
    }
}