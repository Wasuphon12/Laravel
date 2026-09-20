<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\DealerReviewController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'home'])->name('home');
Route::get('/products/{product}', [MarketplaceController::class, 'show'])->name('products.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/read-all', [MessageController::class, 'markAllAsRead'])->name('messages.read-all');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/checkout', [CheckoutController::class, 'create'])->middleware('role:customer')->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('role:customer')->name('checkout.store');
    Route::get('/orders/{order}', [CheckoutController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/payment-slip', [CheckoutController::class, 'submitPaymentSlip'])->middleware('role:customer')->name('orders.payment-slip');
    Route::delete('/orders/{order}', [CheckoutController::class, 'cancel'])->middleware('role:customer')->name('orders.cancel');
    Route::post('/orders/{order}/confirm-delivery', [CheckoutController::class, 'confirmDelivery'])->middleware('role:customer')->name('orders.confirm-delivery');
    Route::post('/order-items/{item}/disputes', [DisputeController::class, 'store'])->middleware('role:customer')->name('disputes.store');
    Route::post('/order-items/{item}/reviews', [DealerReviewController::class, 'store'])->middleware('role:customer')->name('dealer-reviews.store');

    Route::middleware('role:customer')->prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/products/{product}', [CartController::class, 'add'])->name('add');
        Route::delete('/products/{product}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
    });

    Route::middleware('role:dealer')->prefix('dealer')->name('dealer.')->group(function () {
        Route::get('/products', [MarketplaceController::class, 'dealerProducts'])->name('products');
        Route::get('/products/create', [MarketplaceController::class, 'createProduct'])->name('products.create');
        Route::post('/products', [MarketplaceController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{product}/edit', [MarketplaceController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{product}', [MarketplaceController::class, 'updateProduct'])->name('products.update');
        Route::get('/kyc', [MarketplaceController::class, 'kyc'])->name('kyc');
        Route::put('/kyc', [MarketplaceController::class, 'saveKyc'])->name('kyc.save');
        Route::patch('/order-items/{item}/shipping', [MarketplaceController::class, 'updateShipping'])->name('shipping.update');
        Route::patch('/orders/{order}/payment-slip', [CheckoutController::class, 'verifyPaymentSlip'])->name('payment-slip.verify');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::patch('/kyc/{profile}', [AdminController::class, 'updateKyc'])->name('kyc.update');
        Route::patch('/disputes/{dispute}', [AdminController::class, 'updateDispute'])->name('disputes.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
