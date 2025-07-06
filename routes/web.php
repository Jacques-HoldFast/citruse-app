<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => false, // Public registration disabled - users created by admins only
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');
// Add this inside the middleware('auth') group
Route::get('/products', function () {
    return Inertia::render('Products/Index');
})->name('products.index');
// Protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Products
    Route::get('/products', function () {
        return Inertia::render('Products/Index');
    })->name('products.index');

    Route::get('/products/{id}', function ($id) {
        return Inertia::render('Products/Show', ['productId' => $id]);
    })->name('products.show');

    // Suppliers (System Admin & Purchasing Manager only)
    Route::middleware('role:system_administrator,purchasing_manager')->group(function () {
        Route::get('/suppliers', function () {
            return Inertia::render('Suppliers/Index');
        })->name('suppliers.index');

        Route::get('/suppliers/create', function () {
            return Inertia::render('Suppliers/Create');
        })->name('suppliers.create');

        Route::get('/suppliers/{id}', function ($id) {
            return Inertia::render('Suppliers/Show', ['supplierId' => $id]);
        })->name('suppliers.show');

        Route::get('/suppliers/{id}/edit', function ($id) {
            return Inertia::render('Suppliers/Edit', ['supplierId' => $id]);
        })->name('suppliers.edit');
    });

    // Distributors (System Admin & Purchasing Manager only)
    Route::middleware('role:system_administrator,purchasing_manager')->group(function () {
        Route::get('/distributors', function () {
            return Inertia::render('Distributors/Index');
        })->name('distributors.index');

        Route::get('/distributors/create', function () {
            return Inertia::render('Distributors/Create');
        })->name('distributors.create');

        Route::get('/distributors/{id}', function ($id) {
            return Inertia::render('Distributors/Show', ['distributorId' => $id]);
        })->name('distributors.show');

        Route::get('/distributors/{id}/edit', function ($id) {
            return Inertia::render('Distributors/Edit', ['distributorId' => $id]);
        })->name('distributors.edit');
    });

    // Purchase Orders Views 
    Route::get('/purchase-orders', function () {
        return Inertia::render('PurchaseOrders/Index');
    })->name('purchase-orders.index');

    Route::get('/purchase-orders/reports/forecast', function () {
        return Inertia::render('PurchaseOrders/PipelineForecast');
    })->name('purchase-orders.forecast');


    // Purchase Orders  Edit / Create (System Admin & Purchasing Manager only)
    Route::middleware('role:system_administrator,purchasing_manager')->group(function () {

        Route::get('/purchase-orders/create', function () {
            return Inertia::render('PurchaseOrders/Create');
        })->name('purchase-orders.create');

        Route::get('/purchase-orders/{id}/edit', function ($id) {
            return Inertia::render('PurchaseOrders/Edit', ['orderId' => $id]);
        })->name('purchase-orders.edit');


    });



    Route::get('/purchase-orders/{id}', function ($id) {
        return Inertia::render('PurchaseOrders/Show', ['orderId' => $id]);
    })->name('purchase-orders.show');






    // Users (System Admin only)
    Route::middleware('role:system_administrator')->group(function () {
        Route::get('/users', function () {
            return Inertia::render('Users/Index');
        })->name('users.index');

        Route::get('/users/create', function () {
            return Inertia::render('Users/Create');
        })->name('users.create');

        Route::get('/users/{id}/edit', function ($id) {
            return Inertia::render('Users/Edit', ['userId' => $id]);
        })->name('users.edit');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';