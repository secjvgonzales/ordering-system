<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoPaymentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MayaPaymentController;
use App\Http\Controllers\MayaWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('shop.index');
});

Route::get('/shop', [ShopController::class, 'index'])
    ->name('shop.index');

Route::get('/shop/search', [ShopController::class, 'search'])
    ->name('shop.search');

Route::get('/products/{item}', [ShopController::class, 'show'])
    ->name('products.show');

Route::post('/products/{item}/cart', [CartController::class, 'guestStore'])
    ->name('products.cart.guest');

Route::post('/webhooks/maya', MayaWebhookController::class)
    ->name('webhooks.maya');

/*
|--------------------------------------------------------------------------
| General Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'active'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'admin'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Item Management
        |--------------------------------------------------------------------------
        */

        Route::get('/items', [ItemController::class, 'index'])
            ->name('items.index');

        Route::get('/items/create', [ItemController::class, 'create'])
            ->name('items.create');

        Route::post('/items', [ItemController::class, 'store'])
            ->name('items.store');

        Route::get('/items/{item}', [ItemController::class, 'show'])
            ->name('items.show');

        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])
            ->name('items.edit');

        Route::put('/items/{item}', [ItemController::class, 'update'])
            ->name('items.update');

        Route::delete('/items/{item}', [ItemController::class, 'destroy'])
            ->name('items.destroy');

        Route::patch('/items/{item}/status', [ItemController::class, 'toggleStatus'])
            ->name('items.status');

        Route::get('/orders', [OrderManagementController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [OrderManagementController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])
            ->name('orders.status');

        Route::patch('/orders/{order}/cancel', [OrderManagementController::class, 'cancel'])
            ->name('orders.cancel');

        Route::patch('/orders/{order}/payment', [OrderManagementController::class, 'markPaymentPaid'])
            ->name('orders.payment');
    });

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'staff'])
            ->name('dashboard');

        Route::get('/orders', [OrderManagementController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [OrderManagementController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])
            ->name('orders.status');

        Route::patch('/orders/{order}/cancel', [OrderManagementController::class, 'cancel'])
            ->name('orders.cancel');

        Route::patch('/orders/{order}/payment', [OrderManagementController::class, 'markPaymentPaid'])
            ->name('orders.payment');
    });

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'customer'])
            ->name('dashboard');

        Route::get('/shop', [ShopController::class, 'customerShop'])
            ->name('shop');

        Route::get('/cart', [CartController::class, 'index'])
            ->name('cart.index');

        Route::post('/cart/{item}', [CartController::class, 'store'])
            ->name('cart.store');

        Route::patch('/cart/{item}', [CartController::class, 'update'])
            ->name('cart.update');

        Route::delete('/cart/{item}', [CartController::class, 'destroy'])
            ->name('cart.destroy');

        Route::delete('/cart', [CartController::class, 'clear'])
            ->name('cart.clear');

        Route::get('/checkout', [OrderController::class, 'checkout'])
            ->name('checkout.index');

        Route::post('/checkout', [OrderController::class, 'store'])
            ->name('checkout.store');

        Route::get('/payment/demo/{order}', [DemoPaymentController::class, 'show'])
            ->name('demo-payment.show');

        Route::post('/payment/demo/{order}', [DemoPaymentController::class, 'process'])
            ->name('demo-payment.process');

        Route::get('/payment/maya/success', [MayaPaymentController::class, 'success'])
            ->name('maya.success');

        Route::get('/payment/maya/failure', [MayaPaymentController::class, 'failure'])
            ->name('maya.failure');

        Route::get('/payment/maya/cancel', [MayaPaymentController::class, 'cancel'])
            ->name('maya.cancel');

        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])
            ->name('orders.receipt');

        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');
    });

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
