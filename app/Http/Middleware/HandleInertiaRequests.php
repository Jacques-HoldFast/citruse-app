<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'role_display_name' => $request->user()->getRoleDisplayName(),
                    'permissions' => [
                        'can_manage_suppliers' => $request->user()->canManageSuppliers(),
                        'can_create_purchase_orders' => $request->user()->canCreatePurchaseOrders(),
                        'can_update_purchase_order_status' => $request->user()->canUpdatePurchaseOrderStatus(),
                        'is_system_admin' => $request->user()->isSystemAdmin(),
                        'is_purchasing_manager' => $request->user()->isPurchasingManager(),
                        'is_field_sales_associate' => $request->user()->isFieldSalesAssociate(),
                    ]
                ] : null,
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message')
            ],
        ];
    }
}