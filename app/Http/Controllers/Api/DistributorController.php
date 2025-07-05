<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    /**
     * Display a listing of distributors
     * All authenticated users can view distributors
     */
    public function index(Request $request)
    {
        $query = Distributor::query();

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

        // Include purchase order counts for authorized users
        $user = $request->user();
        if ($user->canManageSuppliers()) {
            $query->withCount(['purchaseOrders', 'activePurchaseOrders']);
        }

        $distributors = $query->orderBy('business_name')->paginate(15);

        return response()->json($distributors);
    }

    /**
     * Store a newly created distributor
     * Only System Admins and Purchasing Managers can create distributors
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'registered_address' => 'required|string',
            'country' => 'required|string|max:100',
            'vat_number' => 'required|string|max:50|unique:distributors,vat_number',
            'primary_sales_contact_name' => 'required|string|max:255',
            'primary_sales_contact_email' => 'required|email|max:255',
            'primary_sales_contact_telephone' => 'required|string|max:50',
            'primary_logistics_contact_name' => 'required|string|max:255',
            'primary_logistics_contact_email' => 'required|email|max:255',
            'primary_logistics_contact_telephone' => 'required|string|max:50',
        ]);

        $distributor = Distributor::create($validated);

        return response()->json([
            'message' => 'Distributor created successfully',
            'distributor' => $distributor
        ], 201);
    }

    /**
     * Display the specified distributor
     */
    public function show(Distributor $distributor, Request $request)
    {
        $user = $request->user();

        // Load purchase orders for authorized users
        if ($user->canManageSuppliers()) {
            $distributor->load([
                'purchaseOrders' => function ($query) {
                    $query->with(['items.product'])->orderBy('created_at', 'desc')->limit(10);
                }
            ]);
        }

        return response()->json($distributor);
    }

    /**
     * Update the specified distributor
     * Only System Admins and Purchasing Managers can update distributors
     */
    public function update(Request $request, Distributor $distributor)
    {
        $validated = $request->validate([
            'business_name' => 'sometimes|required|string|max:255',
            'registered_address' => 'sometimes|required|string',
            'country' => 'sometimes|required|string|max:100',
            'vat_number' => 'sometimes|required|string|max:50|unique:distributors,vat_number,' . $distributor->id,
            'primary_sales_contact_name' => 'sometimes|required|string|max:255',
            'primary_sales_contact_email' => 'sometimes|required|email|max:255',
            'primary_sales_contact_telephone' => 'sometimes|required|string|max:50',
            'primary_logistics_contact_name' => 'sometimes|required|string|max:255',
            'primary_logistics_contact_email' => 'sometimes|required|email|max:255',
            'primary_logistics_contact_telephone' => 'sometimes|required|string|max:50',
        ]);

        $distributor->update($validated);

        return response()->json([
            'message' => 'Distributor updated successfully',
            'distributor' => $distributor
        ]);
    }

    /**
     * Remove the specified distributor
     * Only System Admins can delete distributors
     */
    public function destroy(Distributor $distributor)
    {
        // Check if distributor has any purchase orders
        if ($distributor->purchaseOrders()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete distributor with existing purchase orders'
            ], 422);
        }

        $distributor->delete();

        return response()->json(['message' => 'Distributor deleted successfully']);
    }
}