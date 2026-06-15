<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', [\App\Http\Controllers\Tenant\ShopController::class, 'home'])->name('shop.home');
    Route::get('/category/{slug}', [\App\Http\Controllers\Tenant\ShopController::class, 'category'])->name('shop.category');
    Route::get('/product/{slug}', [\App\Http\Controllers\Tenant\ShopController::class, 'product'])->name('shop.product');

    Route::get('/cart', [\App\Http\Controllers\Tenant\CartController::class, 'index'])->name('shop.cart');
    Route::post('/cart/add/{product}', [\App\Http\Controllers\Tenant\CartController::class, 'add'])->name('shop.cart.add');
    Route::post('/cart/remove/{product}', [\App\Http\Controllers\Tenant\CartController::class, 'remove'])->name('shop.cart.remove');

    Route::get('/checkout', [\App\Http\Controllers\Tenant\CheckoutController::class, 'index'])->name('shop.checkout');
    Route::post('/checkout', [\App\Http\Controllers\Tenant\CheckoutController::class, 'store'])->name('shop.checkout.store');
});
