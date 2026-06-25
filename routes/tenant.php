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

    // Storefront Routes
    Route::get('/catalog', \App\Livewire\Shop\Catalog::class)->name('shop.catalog');
    Route::get('/collections/{slug}', \App\Livewire\Shop\Catalog::class)->name('shop.category');

    Route::controller(\App\Http\Controllers\Tenant\ShopController::class)->name('shop.')->group(function () {
        Route::get('/', 'home')->name('home');
        Route::get('/products/{slug}', 'product')->name('product');
    });

    // Contact Routes
    Route::controller(\App\Http\Controllers\Tenant\ContactController::class)->name('shop.contact')->group(function () {
        Route::get('/contact', 'show');
        Route::post('/contact', 'send')->name('.send');
    });

    // Cart Routes
    Route::controller(\App\Http\Controllers\Tenant\CartController::class)->prefix('cart')->name('shop.cart.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add/{product}', 'add')->name('add');
        Route::post('/remove/{product}', 'remove')->name('remove');
    });

    // Checkout Routes
    Route::controller(\App\Http\Controllers\Tenant\CheckoutController::class)->prefix('checkout')->name('shop.checkout.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/success', 'success')->name('success');
        Route::get('/cancel', 'cancel')->name('cancel');
    });

    Route::post('/stripe/webhook', [\App\Http\Controllers\Tenant\StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

    // Customer Auth Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Tenant\Auth\LoginController::class, 'showLoginForm'])->name('shop.login');
        Route::post('/login', [\App\Http\Controllers\Tenant\Auth\LoginController::class, 'login']);
        Route::get('/register', [\App\Http\Controllers\Tenant\Auth\RegisterController::class, 'showRegistrationForm'])->name('shop.register');
        Route::post('/register', [\App\Http\Controllers\Tenant\Auth\RegisterController::class, 'register']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Tenant\Auth\LoginController::class, 'logout'])->name('shop.logout');
        Route::get('/profile', [\App\Http\Controllers\Tenant\Auth\ProfileController::class, 'index'])->name('shop.profile');
        Route::post('/products/{product}/review', [\App\Http\Controllers\Tenant\ShopController::class, 'storeReview'])->name('shop.product.review');
    });

    // Admin Routes
    Route::prefix('dashboard')->name('admin.')->group(function () {
        Route::controller(\App\Http\Controllers\Tenant\Admin\LoginController::class)->group(function () {
            Route::get('/login', 'showLoginForm')->name('login');
            Route::post('/login', 'login');
            Route::post('/logout', 'logout')->name('logout');
        });

        Route::middleware([\App\Http\Middleware\TenantAdmin::class])->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\DashboardController::class, 'index'])->name('dashboard');
            
            Route::resource('categories', \App\Http\Controllers\Tenant\Admin\CategoryController::class)->except(['show']);
            Route::resource('products', \App\Http\Controllers\Tenant\Admin\ProductController::class)->except(['show']);
            
            Route::controller(\App\Http\Controllers\Tenant\Admin\OrderController::class)->prefix('orders')->name('orders.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{order}', 'show')->name('show');
                Route::patch('/{order}/status', 'updateStatus')->name('updateStatus');
            });
        });
    });
});
