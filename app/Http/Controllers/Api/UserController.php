<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get the authenticated user
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_display_name' => $user->getRoleDisplayName(),
                'permissions' => [
                    'can_manage_suppliers' => $user->canManageSuppliers(),
                    'can_create_purchase_orders' => $user->canCreatePurchaseOrders(),
                    'can_update_purchase_order_status' => $user->canUpdatePurchaseOrderStatus(),
                    'is_system_admin' => $user->isSystemAdmin(),
                    'is_purchasing_manager' => $user->isPurchasingManager(),
                    'is_field_sales_associate' => $user->isFieldSalesAssociate(),
                ]
            ]
        ]);
    }

    /**
     * Get user dashboard data based on role
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $data = [];

        if ($user->isSystemAdmin()) {
            // System admin gets full overview
            $data = [
                'total_users' => User::count(),
                'total_suppliers' => \App\Models\Supplier::count(),
                'total_distributors' => \App\Models\Distributor::count(),
                'total_products' => \App\Models\Product::count(),
                'active_pod_orders' => \App\Models\PurchaseOrder::distributorOrders()
                    ->whereIn('status', ['new', 'accepted_by_supplier', 'in_delivery'])
                    ->count(),
                'active_pos_orders' => \App\Models\PurchaseOrder::supplierOrders()
                    ->whereIn('status', ['new', 'accepted_by_supplier', 'in_delivery'])
                    ->count(),
                'recent_orders' => \App\Models\PurchaseOrder::with(['supplier', 'distributor', 'createdBy'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ];
        } elseif ($user->isPurchasingManager()) {
            // Purchasing manager gets order-focused data
            $data = [
                'my_pod_orders' => \App\Models\PurchaseOrder::distributorOrders()
                    ->where('created_by', $user->id)
                    ->whereIn('status', ['new', 'accepted_by_supplier', 'in_delivery'])
                    ->count(),
                'pending_pod_orders' => \App\Models\PurchaseOrder::distributorOrders()
                    ->where('status', 'new')
                    ->count(),
                'total_suppliers' => \App\Models\Supplier::count(),
                'total_distributors' => \App\Models\Distributor::count(),
                'recent_pod_orders' => \App\Models\PurchaseOrder::distributorOrders()
                    ->with(['distributor', 'createdBy'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'orders_needing_attention' => \App\Models\PurchaseOrder::distributorOrders()
                    ->where('status', 'new')
                    ->where('created_at', '<', now()->subDays(2))
                    ->count(),
            ];
        } elseif ($user->isFieldSalesAssociate()) {
            // Field sales gets supplier-focused data
            $data = [
                'pending_pos_orders' => \App\Models\PurchaseOrder::supplierOrders()
                    ->where('status', 'new')
                    ->count(),
                'in_delivery_orders' => \App\Models\PurchaseOrder::supplierOrders()
                    ->where('status', 'in_delivery')
                    ->count(),
                'overdue_deliveries' => \App\Models\PurchaseOrderItem::whereHas('purchaseOrder', function ($q) {
                    $q->where('type', 'supplier_order')
                        ->whereIn('status', ['accepted_by_supplier', 'in_delivery']);
                })
                    ->where('required_delivery_date', '<', now())
                    ->whereNull('delivered_quantity_kg')
                    ->count(),
                'recent_pos_orders' => \App\Models\PurchaseOrder::supplierOrders()
                    ->with(['supplier', 'createdBy'])
                    ->whereIn('status', ['new', 'accepted_by_supplier', 'in_delivery'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'deliveries_this_week' => \App\Models\PurchaseOrderItem::whereHas('purchaseOrder', function ($q) {
                    $q->where('type', 'supplier_order');
                })
                    ->whereBetween('required_delivery_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
            ];
        }

        return response()->json([
            'dashboard' => $data,
            'user_role' => $user->role
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Get all users (System Admin only)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(15);

        return response()->json($users);
    }

    /**
     * Create new user (System Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['system_administrator', 'purchasing_manager', 'field_sales_associate'])],
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Update user (System Admin only)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'role' => ['sometimes', 'required', Rule::in(['system_administrator', 'purchasing_manager', 'field_sales_associate'])],
            'password' => 'sometimes|nullable|string|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Delete user (System Admin only)
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        // Check if user has created purchase orders
        if ($user->purchaseOrders()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete user who has created purchase orders'
            ], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}