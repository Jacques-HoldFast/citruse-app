<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\DistributorController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


// Public routes (no authentication required)
Route::get('/products/catalog', [ProductController::class, 'catalog']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {

    // User routes (all authenticated users)
    Route::get('/user', [UserController::class, 'show']);
    Route::get('/user/dashboard', [UserController::class, 'dashboard']);
    Route::patch('/user/profile', [UserController::class, 'updateProfile']);

    // Products (all authenticated users can view)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/products/pricing/years', [ProductController::class, 'pricingYears']);
    Route::get('/products/pricing', [ProductController::class, 'pricing']);

    // Purchase Orders (role-based access)
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show']);
    Route::get('/purchase-orders/reports/pipeline-forecast', [PurchaseOrderController::class, 'pipelineForecast']);

    // Suppliers and Distributors (all can view, limited create/update)
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);
    Route::get('/distributors', [DistributorController::class, 'index']);
    Route::get('/distributors/{distributor}', [DistributorController::class, 'show']);

    // System Admin and Purchasing Manager routes
    Route::middleware('role:system_administrator,purchasing_manager')->group(function () {

        // Purchase Orders - Create and full update
        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store']);
        Route::patch('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update']);

        // Suppliers - Full CRUD
        Route::post('/suppliers', [SupplierController::class, 'store']);
        Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update']);
        Route::get('/suppliers/{supplier}/performance', [SupplierController::class, 'performance']);

        // Distributors - Full CRUD
        Route::post('/distributors', [DistributorController::class, 'store']);
        Route::patch('/distributors/{distributor}', [DistributorController::class, 'update']);
    });

    // Field Sales Associate and System Admin routes
    Route::middleware('role:system_administrator,field_sales_associate')->group(function () {

        // Purchase Order line item updates (delivery tracking)
        Route::patch('/purchase-order-items/{item}', [PurchaseOrderController::class, 'updateLineItem']);
    });

    // System Admin only routes
    Route::middleware('role:system_administrator')->group(function () {

        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        // Products - Full CRUD
        Route::post('/products', [ProductController::class, 'store']);
        Route::patch('/products/{product}', [ProductController::class, 'update']);
        Route::patch('/products/{product}/pricing', [ProductController::class, 'updatePricing']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Delete routes (destructive actions)
        Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy']);
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);
        Route::delete('/distributors/{distributor}', [DistributorController::class, 'destroy']);
    });
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found'
    ], 404);
});