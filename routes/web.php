<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'customer.home')->name('customer.home');
Route::view('/home', 'customer.home');
Route::view('/login', 'auth.login');
Route::view('/register', 'auth.register');
Route::view('/vendor/register', 'vendor.register');
Route::view('/vendor/login', 'auth.login');
Route::view('/vendor/profile', 'vendor.profile');
Route::view('/vendor/address', 'vendor.address');
Route::view('/vendor/bank-account', 'vendor.bank-account');
Route::view('/vendor/documents', 'vendor.documents');
Route::view('/vendor/review', 'vendor.review');
Route::view('/vendor/pending', 'vendor.pending');
Route::view('/vendor/rejected', 'vendor.rejected');

// Customer storefront
Route::view('/categories', 'customer.categories')->name('customer.categories');
Route::view('/products/{slug}', 'customer.product')->name('customer.product');
Route::view('/cart', 'customer.cart')->name('customer.cart');
Route::view('/checkout', 'customer.checkout')->name('customer.checkout');
Route::view('/wishlist', 'customer.wishlist')->name('customer.wishlist');
Route::view('/orders', 'customer.orders')->name('customer.orders');
Route::get('/orders/{order}', function ($order) {
    return view('customer.order-detail', ['orderId' => $order]);
})->whereNumber('order')->name('customer.orders.show');
Route::view('/account', 'customer.account')->name('customer.account');
Route::view('/account/settings', 'customer.account')->name('customer.account.settings');
Route::view('/shop', 'customer.shop')->name('customer.shop');

Route::get('/vendor/dashboard', function () {
    return view('vendor.dashboard');
})->name('vendor.dashboard');

Route::get('/vendor/products', function () {
    return view('vendor.products.index');
})->name('vendor.products.index');

Route::get('/vendor/products/{product}/manage', function ($product) {
    return view('vendor.products.manage', ['productId' => $product]);
})->name('vendor.products.manage');

Route::get('/vendor/orders', function () {
    return view('vendor.orders.index');
})->name('vendor.orders.index');

Route::get('/vendor/orders/{subOrder}', function ($subOrder) {
    return view('vendor.orders.show', ['subOrderId' => $subOrder]);
})->name('vendor.orders.show');

Route::post('/vendor/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('vendor.logout');

// Route::view('/admin/dashboard', 'admin.dashboard')
//     ->name('admin.dashboard');

// routes/web.php
// Add this alongside your existing /admin/dashboard route.
// No Laravel auth middleware here — page load is public, but the
// blade layout's inline JS checks localStorage('meeta_token') and
// redirects to /admin/login if missing. Real protection happens on
// the API side via auth:sanctum + role:admin (already in your api.php).

Route::prefix('admin')->group(function () {

    Route::get('/vendors', function () {
        return view('admin.vendors.index');
    });

    Route::get('/categories', function () {
        return view('admin.categories.index');
    });
    Route::get('/brands', function () {
        return view('admin.brands.index');
    });

    Route::get('/attributes', function () {
        return view('admin.attributes.index');
    });

    Route::get('/products/pending', function () {
        return view('admin.products.index');
    });

    Route::get('/orders', function () {
        return view('admin.orders.index');
    });

    Route::get('/payouts', function () {
        return view('admin.payouts.index');
    });

    Route::get('/commissions', function () {
        return view('admin.commissions.index');
    });

    Route::get('/disputes', function () {
        return view('admin.disputes.index');
    });

    // If you don't already have these two, add them too —
    // they're referenced by the sidebar/layout guard:
    Route::get('/login', function () {
        return view('auth.login');
    });

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
});
