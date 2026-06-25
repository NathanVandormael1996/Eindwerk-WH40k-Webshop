# Adept's Armoury — Full Detailed Codebase Documentation

> Stack: **Laravel 13 · Livewire 4 · Stancl Tenancy 3 · Stripe · Tailwind CSS**

---

## Table of Contents
1. [Project Overview](#1-project-overview)
2. [Tech Stack & composer.json](#2-tech-stack)
3. [Multi-Tenancy Architecture](#3-multi-tenancy-architecture)
4. [Routing](#4-routing)
5. [Database Schema](#5-database-schema)
6. [Models In Depth](#6-models-in-depth)
7. [Controllers In Depth](#7-controllers-in-depth)
8. [Livewire Catalog Component](#8-livewire-catalog-component)
9. [Middleware](#9-middleware)
10. [Blade Layouts](#10-blade-layouts)
11. [Storefront Views](#11-storefront-views)
12. [Admin Panel Views](#12-admin-panel-views)
13. [Stripe Payment Flow](#13-stripe-payment-flow)
14. [Design System](#14-design-system)

---

## 1. Project Overview

**Adept's Armoury** is a Warhammer 40K–themed multi-tenant e-commerce platform. One codebase powers multiple independent shops, each on its own subdomain with its own isolated database.

### What it sells
| Category | Slug | Attribute filter shown |
|---|---|---|
| Paints | `paints` | Color |
| Figurines | `figurines` | Faction |
| Videogames | `videogames` | Genre |
| Boardgames | `boardgames` | Players |
| Apparel | `apparel` | Type |
| Comics | `comics` | — |

### Two user types
- **Customers** — register/login per-shop, place orders, leave reviews
- **Shop Admins** — access `/dashboard`, manage products/categories/orders, trigger Stripe refunds

---

## 2. Tech Stack

### composer.json (key deps)
```json
{
  "require": {
    "php": "^8.3",
    "laravel/framework": "^13.7",
    "livewire/livewire": "^4.0",
    "livewire/flux": "^2.14",
    "stancl/tenancy": "^3.10",
    "stripe/stripe-php": "^20.2"
  },
  "require-dev": {
    "laravel/breeze": "^2.4",
    "pestphp/pest": "^4.7"
  }
}
```

### Frontend
- **Tailwind CSS** — utility classes throughout all blade files
- **Vite** — asset bundler (`vite.config.js`)
- **Cinzel** (serif) — WH40K gothic heading font
- **Outfit** (sans) — clean body font
- Both loaded from Bunny Fonts CDN in the tenant layout head

### Dev workflow (composer dev script)
```bash
# Runs 4 processes concurrently:
php artisan serve          # Laravel dev server
php artisan queue:listen   # Job queue
php artisan pail           # Log tailer
npm run dev                # Vite HMR
```

---

## 3. Multi-Tenancy Architecture

Uses **stancl/tenancy v3** with domain-based tenant identification.

### Two databases

```
┌─────────────────────────────────────────────────┐
│  CENTRAL DATABASE (shared)                       │
│  tables: tenants, domains, countries, admins,    │
│          sessions                                │
└──────────────────────┬──────────────────────────┘
                       │  one row per shop
          ┌────────────┴────────────┐
          ▼                         ▼
┌─────────────────┐       ┌─────────────────┐
│ TENANT DB: shop1│       │ TENANT DB: shop2│
│ users, products │       │ users, products │
│ categories,     │       │ categories,     │
│ orders, reviews │       │ orders, reviews │
│ physical_stores │       │ physical_stores │
│ product_stocks  │       │ product_stocks  │
└─────────────────┘       └─────────────────┘
```

### Shop model (the Tenant)
`app/Models/Shop.php`
```php
class Shop extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return ['id', 'country_id', 'name', 'currency'];
    }
}
```
- Extends Stancl's `BaseTenant`
- `HasDatabase` — gives it an isolated MySQL database
- `HasDomains` — associates domain names with this tenant
- Custom columns stored in the central `tenants` table alongside Stancl's default `data` JSON blob

### Request lifecycle
```
browser request → nginx → Laravel
        ↓
InitializeTenancyByDomain middleware
  reads request host → looks up in domains table → finds tenant
  switches DB connection to tenant's database
        ↓
PreventAccessFromCentralDomains middleware
  blocks if request came from a central domain
        ↓
Route matched → Controller → Eloquent (now queries tenant DB)
        ↓
Blade / Livewire response
```

### tenants table schema
```php
Schema::create('tenants', function (Blueprint $table) {
    $table->string('id')->primary();       // e.g. "adepts-armoury"
    $table->foreignId('country_id')->constrained()->cascadeOnDelete();
    $table->string('name');                // "Adept's Armoury"
    $table->string('currency')->default('EUR');
    $table->timestamps();
    $table->json('data')->nullable();      // Stancl metadata blob
});
```

### domains table
Maps `adepts-armoury.example.com` → tenant id `adepts-armoury`. Stancl manages this automatically.


---

## 4. Routing

### routes/web.php — Central domain
```php
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', fn() => redirect('/login'));

        Route::view('dashboard', 'dashboard')
            ->middleware(['auth', 'verified'])
            ->name('dashboard');

        Route::view('profile', 'profile')
            ->middleware(['auth'])
            ->name('profile');

        require __DIR__.'/auth.php';  // Laravel Breeze auth routes
    });
}
```
The `foreach` loop is needed because central domains is an array in config. All central routes are scoped to those domains only.

### routes/tenant.php — Tenant storefront + admin

All routes are wrapped in:
```php
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // all tenant routes here
});
```

#### Storefront
```php
// Livewire full-page component routes
Route::get('/catalog', \App\Livewire\Shop\Catalog::class)->name('shop.catalog');
Route::get('/collections/{slug}', \App\Livewire\Shop\Catalog::class)->name('shop.category');

// Standard controller routes
Route::controller(ShopController::class)->name('shop.')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/products/{slug}', 'product')->name('product');
});
```
> Note: `/catalog` and `/collections/{slug}` both point to the same `Catalog` Livewire component. The slug is passed via `mount()`.

#### Cart (session-based, no DB)
```php
Route::controller(CartController::class)->prefix('cart')->name('shop.cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/add/{product}', 'add')->name('add');
    Route::post('/remove/{product}', 'remove')->name('remove');
});
```

#### Checkout + Stripe Webhook
```php
Route::controller(CheckoutController::class)->prefix('checkout')->name('shop.checkout.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');       // creates Stripe session
    Route::get('/success', 'success')->name('success');
    Route::get('/cancel', 'cancel')->name('cancel');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook');
```

#### Customer auth (guest/auth middleware)
```php
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('shop.login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('shop.register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('shop.logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('shop.profile');
    Route::post('/products/{product}/review', [ShopController::class, 'storeReview'])
        ->name('shop.product.review');
});
```

#### Admin panel (prefix: /dashboard)
```php
Route::prefix('dashboard')->name('admin.')->group(function () {
    // Public admin login
    Route::controller(Admin\LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::post('/logout', 'logout')->name('logout');
    });

    // Protected by TenantAdmin middleware
    Route::middleware([TenantAdmin::class])->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', Admin\CategoryController::class)->except(['show']);
        Route::resource('products', Admin\ProductController::class)->except(['show']);

        Route::controller(Admin\OrderController::class)->prefix('orders')->name('orders.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{order}', 'show')->name('show');
            Route::patch('/{order}/status', 'updateStatus')->name('updateStatus');
        });
    });
});
```
`Route::resource` generates: index, create, store, edit, update, destroy (minus `show` which is excluded).


---

## 5. Database Schema

### Central migrations (database/migrations/)

#### countries
```php
$table->id();
$table->string('code', 2);   // ISO 3166 e.g. "NL", "DE"
$table->string('name');
$table->timestamps();
```

#### admins (central global admins)
```php
$table->id();
$table->string('name');
$table->string('email')->unique();
$table->string('password');
$table->string('role')->default('admin');
$table->timestamps();
```
These are separate from per-tenant users. Admin credentials are stored centrally; the `is_admin` flag lives on the tenant `users` table.

### Tenant migrations (database/migrations/tenant/)

#### users (per-tenant customers)
Standard Laravel users table. Extended by migration `2026_06_15_...`:
```php
// Added fields:
$table->string('address')->nullable();
$table->string('city')->nullable();
$table->string('postal_code')->nullable();
$table->boolean('is_admin')->default(false);
```
The `is_admin` column on tenant users lets shop owners grant admin access to a regular user account.

#### categories
```php
$table->id();
$table->string('name');    // "Paints", "Figurines", etc.
$table->string('slug');    // "paints", "figurines", etc.
$table->timestamps();
```

#### products (base table)
```php
$table->id();
$table->foreignId('category_id')->constrained()->cascadeOnDelete();
$table->string('name');
$table->string('slug');          // URL-safe name, auto-generated
$table->text('description')->nullable();
$table->integer('price');        // CENTS, e.g. 2499 = €24.99
$table->timestamps();
```

Then extended by 3 additive migrations:
```php
// 2026_06_20 — image_url
$table->string('image_url')->nullable();

// 2026_06_21 — tags (JSON array for flexible labelling)
$table->json('tags')->nullable();
// example value: ["new", "featured", "sale"]

// 2026_06_21 — attributes (JSON object for category-specific filters)
$table->json('attributes')->nullable();
// example for figurine: {"faction": "Space Marines"}
// example for paint:    {"color": "Abaddon Black"}
// example for game:     {"genre": "Strategy", "players": "1-4"}
```

> **Price in cents**: All prices are stored as integers (cents). Dividing by 100 happens only at display time: `number_format($product->price / 100, 2)`. Stripe also expects cents, so no conversion is needed for payment.

#### orders
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->integer('total_amount');       // cents
$table->string('status');              // pending|paid|shipped|delivered|cancelled
$table->string('stripe_session_id')->nullable();
$table->timestamps();
// Extended migration adds: address, city, postal_code on the order itself
```

#### order_items
```php
$table->id();
$table->foreignId('order_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->integer('quantity');
$table->integer('unit_price');   // snapshot of price at time of purchase (cents)
$table->timestamps();
```
> `unit_price` is a snapshot — if the product price changes later, historical orders remain accurate.

#### reviews
```php
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->unsignedTinyInteger('rating');  // 1–5
$table->text('comment');
$table->timestamps();
```

#### physical_stores
```php
$table->id();
$table->string('name');
$table->string('address')->nullable();
$table->string('city')->nullable();
$table->boolean('is_central_warehouse')->default(false);
$table->timestamps();
```
The `is_central_warehouse` flag is the key field. The online shop only sells from the central warehouse. Physical retail locations are tracked separately but displayed on the product page.

#### product_stocks
```php
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('physical_store_id')->constrained()->cascadeOnDelete();
$table->integer('quantity')->default(0);
$table->timestamps();
```
This creates an N:N relationship between products and stores, with `quantity` as the join payload. A product can have stock in multiple locations.


---

## 6. Models In Depth

### Category (`app/Models/Category.php`)
```php
class Category extends Model
{
    protected $guarded = [];      // all fields mass-assignable

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```
Simple. Used with `withCount('products')` in the Catalog Livewire component to show product counts per category.

---

### Product (`app/Models/Product.php`)
```php
class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tags'       => 'array',   // JSON column auto-decoded to PHP array
        'attributes' => 'array',   // JSON column auto-decoded to PHP array
    ];

    // --- Relationships ---

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    // --- Computed / Accessor attributes ---

    // Average star rating across all reviews (0 if none)
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Stock available for ONLINE orders (central warehouse only)
    public function getStockAttribute()
    {
        return $this->productStocks()
            ->whereHas('physicalStore', function ($query) {
                $query->where('is_central_warehouse', true);
            })->sum('quantity');
    }

    // Total stock across ALL physical stores
    public function getTotalStockAttribute()
    {
        return $this->productStocks()->sum('quantity');
    }
}
```

#### How accessors work
Laravel's `get{Name}Attribute()` convention makes these available as `$product->average_rating`, `$product->stock`, and `$product->total_stock`. They are computed on-demand (not stored in DB).

#### JSON casts in practice
```php
// Storing:
$product->attributes = ['faction' => 'Space Marines', 'scale' => '28mm'];
$product->save();
// Laravel serializes to JSON: {"faction":"Space Marines","scale":"28mm"}

// Reading:
$product->attributes['faction']  // "Space Marines" — PHP array, no json_decode needed
$product->tags                   // PHP array e.g. ["featured", "new"]
```

---

### Order (`app/Models/Order.php`)
```php
class Order extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

#### Status lifecycle
```
pending  →  paid  →  shipped  →  delivered
                ↓
           cancelled  (triggers Stripe refund if paid/shipped/delivered)
```

---

### Review (`app/Models/Review.php`)
```php
class Review extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'rating', 'comment'];

    public function product() { return $this->belongsTo(Product::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
```

---

### PhysicalStore (`app/Models/PhysicalStore.php`)
```php
class PhysicalStore extends Model
{
    protected $guarded = [];

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }
}
```

---

### ProductStock (`app/Models/ProductStock.php`)
```php
class ProductStock extends Model
{
    protected $guarded = [];

    public function product()       { return $this->belongsTo(Product::class); }
    public function physicalStore() { return $this->belongsTo(PhysicalStore::class); }
}
```

#### How stock lookup works end-to-end
```php
// On the product detail page, this eager load runs:
$product = Product::where('slug', $slug)
    ->with(['reviews.user', 'productStocks.physicalStore'])
    ->firstOrFail();

// $product->stock uses the accessor:
//   1. Queries product_stocks JOIN physical_stores
//   2. WHERE physical_stores.is_central_warehouse = true
//   3. SUM(quantity)
// This gives the "online available" number.

// On the product page, physical store breakdown:
$product->productStocks
    ->where('physicalStore.is_central_warehouse', false)
    // -> shows each non-warehouse store with its local quantity
```

---

### Admin (`app/Models/Admin.php`)
```php
class Admin extends Authenticatable  // extends Laravel's User base
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden   = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];  // auto-bcrypt on assignment
    }
}
```
Lives in the **central** database. Completely separate from tenant `User` records.

---

### User (`app/Models/User.php`)
```php
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
```
Uses PHP 8 attribute syntax (`#[Fillable]`, `#[Hidden]`) instead of class properties — functionally identical. Lives in the **tenant** database.

---

### Shop (`app/Models/Shop.php`)
```php
class Shop extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return ['id', 'country_id', 'name', 'currency'];
    }
}
```
`getCustomColumns()` tells Stancl which columns to store directly in the `tenants` table vs. in the `data` JSON blob.


---

## 7. Controllers In Depth

### ShopController (`app/Http/Controllers/Tenant/ShopController.php`)

#### `home(Request $request)`
```php
public function home(Request $request)
{
    $search     = $request->input('search');
    $categoryId = $request->input('category');
    $tag        = $request->input('tag');

    $allCategories = Category::all();

    // If any filter params present, hand off to Livewire catalog
    if ($search || $categoryId || $tag) {
        return redirect()->route('shop.catalog', [
            'search'             => $search,
            'selectedCategories' => $categoryId ? [$categoryId] : [],
            'tag'                => $tag,
        ]);
    }

    // Homepage: load each category with its 4 newest products
    $categorySections = Category::with(['products' => function($query) {
        $query->latest()->take(4);
    }])->get();

    return view('shop.home', compact('categorySections', 'allCategories'));
}
```
The homepage does **not** handle filtering itself. Any search/filter redirects to the Livewire Catalog component which handles all reactive filtering. This keeps the homepage simple.

#### `product($slug)`
```php
public function product($slug)
{
    $product = Product::where('slug', $slug)
        ->with(['reviews.user', 'productStocks.physicalStore'])
        ->firstOrFail();

    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->inRandomOrder()
        ->take(4)
        ->get();

    return view('shop.product', compact('product', 'relatedProducts'));
}
```
Key points:
- Eager loads `reviews.user` in one query (not N+1)
- Eager loads `productStocks.physicalStore` for the stock-by-location display
- Gets 4 random same-category products for the "Related Relics" section

#### `storeReview(Request $request, Product $product)`
```php
public function storeReview(Request $request, Product $product)
{
    $request->validate([
        'rating'  => 'required|integer|min:1|max:5',
        'comment' => 'required|string|max:1000',
    ]);

    $product->reviews()->create([
        'user_id' => auth()->id(),
        'rating'  => $request->rating,
        'comment' => $request->comment,
    ]);

    return back()->with('success', 'Your review has been recorded in the archives.');
}
```
Only accessible to authenticated users (protected by `auth` middleware in routes). Creates review via relationship so `product_id` is set automatically.

---

### CartController (`app/Http/Controllers/Tenant/CartController.php`)

The cart is **100% session-based**. No database table. The session key `cart` holds an array:
```php
// Session structure:
$_SESSION['cart'] = [
    42 => 2,   // product_id => quantity
    17 => 1,
    99 => 3,
];
```

#### `index()`
```php
public function index()
{
    $cart     = session()->get('cart', []);
    $products = collect();
    $total    = 0;

    if (count($cart) > 0) {
        $products = Product::whereIn('id', array_keys($cart))->get();
        foreach ($products as $product) {
            $product->cart_quantity = $cart[$product->id];  // dynamic prop
            $total += $product->price * $product->cart_quantity;
        }
    }

    return view('shop.cart', compact('products', 'total'));
}
```
Note: `$product->cart_quantity` is dynamically set on the Eloquent model instance — it is NOT a database column. This is valid PHP (Eloquent models are dynamic objects) but it's a runtime-only property.

#### `add(Request $request, Product $product)`
```php
public function add(Request $request, Product $product)
{
    $cart     = session()->get('cart', []);
    $quantity = $request->input('quantity', 1);

    if (isset($cart[$product->id])) {
        $cart[$product->id] += $quantity;   // increment existing
    } else {
        $cart[$product->id] = $quantity;    // add new entry
    }

    session()->put('cart', $cart);
    return redirect()->back()->with('success', 'Product added to cart!');
}
```

#### `remove(Product $product)`
```php
public function remove(Product $product)
{
    $cart = session()->get('cart', []);
    unset($cart[$product->id]);
    session()->put('cart', $cart);
    return redirect()->back()->with('success', 'Product removed from cart!');
}
```

---

### CheckoutController (`app/Http/Controllers/Tenant/CheckoutController.php`)

#### `store(StoreCheckoutRequest $request)` — the core checkout
```php
public function store(StoreCheckoutRequest $request)
{
    $cart     = session()->get('cart', []);
    $products = Product::whereIn('id', array_keys($cart))->get();
    $lineItems = [];
    $totalAmount = 0;

    foreach ($products as $product) {
        $quantity     = $cart[$product->id];
        $totalAmount += $product->price * $quantity;

        $lineItems[] = [
            'price_data' => [
                'currency'     => 'eur',
                'product_data' => ['name' => $product->name],
                'unit_amount'  => $product->price,  // already in cents
            ],
            'quantity' => $quantity,
        ];
    }

    DB::beginTransaction();
    try {
        // Creates account if email is new, reuses if existing
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name'     => $request->name,
                'password' => Hash::make(Str::random(16)),  // random pw
            ]
        );

        $order = Order::create([
            'user_id'      => $user->id,
            'total_amount' => $totalAmount,
            'status'       => 'pending',
        ]);

        foreach ($products as $product) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $cart[$product->id],
                'unit_price' => $product->price,  // price snapshot
            ]);
        }

        // Create Stripe Checkout Session
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'success_url'          => route('shop.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('shop.checkout.cancel'),
            'customer_email'       => $request->email,
            'metadata'             => [
                'order_id'  => $order->id,
                'tenant_id' => tenant('id'),  // which shop this belongs to
            ],
        ]);

        $order->update(['stripe_session_id' => $checkoutSession->id]);
        DB::commit();

        // Redirect user to Stripe's hosted payment page
        return redirect($checkoutSession->url);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to place order. ' . $e->getMessage());
    }
}
```

Key design decisions:
- **`firstOrCreate` for users**: Guests can checkout without registering. If they use an existing email, their order is linked to their existing account. New emails get a new account with a random password (they can reset later).
- **DB transaction wraps everything before Stripe**: If Stripe fails, the DB rolls back. If the DB fails before Stripe, we never call Stripe. This prevents orphaned orders.
- **`tenant('id')`**: Stancl helper that returns the current tenant's ID. Stored in Stripe metadata so the webhook knows which shop's DB to update.

#### `success(Request $request)`
```php
public function success(Request $request)
{
    $sessionId = $request->get('session_id');
    $order = Order::where('stripe_session_id', $sessionId)->first();

    if ($order && $order->status === 'pending') {
        $order->update(['status' => 'paid']);
        session()->forget('cart');   // clear the cart
        return redirect()->route('shop.home')->with('success', 'Payment successful!');
    }

    return redirect()->route('shop.home');
}
```
Checks `status === 'pending'` before updating. This is idempotent — if the webhook already set it to `paid`, this is a no-op.

---

### StripeWebhookController (`app/Http/Controllers/Tenant/StripeWebhookController.php`)

```php
public function handleWebhook(Request $request)
{
    $payload       = $request->getContent();      // raw body
    $sigHeader     = $request->header('Stripe-Signature');
    $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

    try {
        // Verifies the HMAC signature to prevent spoofed webhooks
        $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
    } catch (\UnexpectedValueException $e) {
        return response()->json(['error' => 'Invalid payload'], 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return response()->json(['error' => 'Invalid signature'], 400);
    }

    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;
        $orderId = $session->metadata->order_id ?? null;

        // Try finding by metadata order_id first, fall back to session id
        $order = $orderId
            ? Order::find($orderId)
            : Order::where('stripe_session_id', $session->id)->first();

        if ($order && $order->status === 'pending') {
            $order->update(['status' => 'paid']);
        }
    }

    return response()->json(['status' => 'success']);  // Stripe needs 200
}
```
This is the **authoritative** payment confirmation. The `success` redirect can be bypassed by the user closing the tab, but Stripe's webhook will always fire server-to-server.


---

### Admin Controllers

#### Admin\DashboardController
```php
public function index()
{
    $totalProducts = Product::count();
    $recentOrders  = Order::with('user')->latest()->take(5)->get();
    $totalRevenue  = Order::where('status', 'paid')->sum('total_amount');

    return view('admin.dashboard', compact('totalProducts', 'recentOrders', 'totalRevenue'));
}
```
Note: `$totalRevenue` is in cents (matches the DB). The view divides by 100: `€{{ number_format($totalRevenue / 100, 2) }}`.

---

#### Admin\CategoryController
Full CRUD. `store()` and `update()` auto-generate the slug:
```php
public function store(Request $request)
{
    $request->validate(['name' => 'required|string|max:255']);
    Category::create([
        'name' => $request->name,
        'slug' => Str::slug($request->name),  // "Warhammer Paints" -> "warhammer-paints"
    ]);
    return redirect()->route('admin.categories.index')->with('success', 'Category created.');
}
```

---

#### Admin\ProductController
```php
public function index(Request $request)
{
    $query = Product::query()->with('category');

    // Conditional filters
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $products   = $query->paginate(20)->withQueryString();  // preserves filters in pagination links
    $categories = Category::all();

    return view('admin.products.index', compact('products', 'categories'));
}

public function store(Request $request)
{
    $request->validate([
        'name'        => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'description' => 'nullable|string',
        'price'       => 'required|integer|min:0',  // expects CENTS from the form
        'stock'       => 'required|integer|min:0',
    ]);

    Product::create([
        'name'        => $request->name,
        'slug'        => Str::slug($request->name),
        'category_id' => $request->category_id,
        'description' => $request->description,
        'price'       => $request->price,
        'stock'       => $request->stock,
    ]);

    return redirect()->route('admin.products.index')->with('success', 'Product created.');
}
```

---

#### Admin\OrderController — updateStatus with automatic Stripe refund
```php
public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
{
    $oldStatus = $order->status;
    $newStatus = $request->status;
    $refundMsg = '';

    // Automatic refund when cancelling a paid order
    if (
        $newStatus === 'cancelled' &&
        in_array($oldStatus, ['paid', 'shipped', 'delivered']) &&
        $order->stripe_session_id
    ) {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Step 1: Get the Checkout Session to find the PaymentIntent ID
            $session         = Session::retrieve($order->stripe_session_id);
            $paymentIntentId = $session->payment_intent;

            if ($paymentIntentId) {
                // Step 2: Issue a full refund
                Refund::create(['payment_intent' => $paymentIntentId]);
                $refundMsg = ' Stripe refund of €' . number_format($order->total_amount / 100, 2) . ' issued.';
            }
        } catch (\Exception $e) {
            // If refund fails, DO NOT change the order status
            return redirect()->back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }

    $order->update(['status' => $newStatus]);
    return redirect()->route('admin.orders.show', $order)
        ->with('success', 'Order status updated.' . $refundMsg);
}
```

#### Admin\Auth\LoginController — admin-specific auth check
```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Extra check: authenticated user must be an admin
        if (Auth::user()->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        } else {
            Auth::logout();   // log out the non-admin who got in
            return back()->withErrors(['email' => 'You do not have administrative privileges.']);
        }
    }

    return back()->withErrors(['email' => 'The provided credentials do not match.']);
}
```
This uses the default `auth` guard which checks the tenant `users` table. After login succeeds, it checks `is_admin` on the user record. Non-admins are immediately logged back out.

---

### Customer Auth Controllers

#### Tenant\Auth\LoginController
```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        // redirect()->intended() respects the originally intended URL before auth redirect
        return redirect()->intended(route('shop.home'))
            ->with('success', 'Welcome back, battle-brother.');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our sacred records.',
    ])->onlyInput('email');
}
```

#### Tenant\Auth\RegisterController
```php
public function register(Request $request)
{
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // 'confirmed' requires a matching 'password_confirmation' field
        // Password::defaults() enforces min length, mixed case, etc.
    ]);

    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);   // auto-login after registration
    return redirect()->route('shop.home')
        ->with('success', 'Your recruitment into the Adeptus is complete.');
}
```

#### Tenant\Auth\ProfileController
```php
public function index(Request $request)
{
    $orders = Order::where('user_id', $request->user()->id)
        ->with('items.product')   // eager load: orders -> items -> product
        ->orderBy('created_at', 'desc')
        ->get();

    return view('shop.auth.profile', [
        'user'   => $request->user(),
        'orders' => $orders
    ]);
}
```
Loads full order history with items and product details in one efficient query chain.


---

## 8. Livewire Catalog Component

**File**: `app/Livewire/Shop/Catalog.php`
**View**: `resources/views/livewire/shop/catalog.blade.php`

This is the most complex piece of the frontend. It replaces the old static category pages with a fully reactive, no-page-reload filter experience.

### Component class
```php
class Catalog extends Component
{
    use WithPagination;

    // --- Public reactive properties (two-way bound to UI) ---
    public $search           = '';
    public $selectedCategory = null;
    public $selectedFactions = [];
    public $selectedTypes    = [];
    public $selectedColors   = [];
    public $selectedGenres   = [];
    public $selectedPlayers  = [];
    public $sort             = 'newest';
    public $categorySlug     = null;  // NOT in query string (internal use)

    // --- URL query string sync ---
    protected $queryString = [
        'search'             => ['except' => ''],
        'selectedCategory'   => ['except' => null],
        'selectedFactions'   => ['except' => []],
        'selectedTypes'      => ['except' => []],
        'selectedColors'     => ['except' => []],
        'selectedGenres'     => ['except' => []],
        'selectedPlayers'    => ['except' => []],
        'sort'               => ['except' => 'newest'],
    ];
```
`['except' => '']` means: only include this parameter in the URL if it differs from the default value. So a clean URL is `/catalog` not `/catalog?search=&sort=newest&selectedFactions=[]`.

### mount() — initialise from URL slug
```php
public function mount($slug = null)
{
    if ($slug) {
        $this->categorySlug = $slug;
        $cat = Category::where('slug', $slug)->first();
        if ($cat) {
            $this->selectedCategory = (string)$cat->id;
            // cast to string because query string values are always strings
        }
    }
}
```
When visiting `/collections/figurines`, `$slug = "figurines"`. The component looks up the category and pre-sets the filter. This is how the `/collections/{slug}` route feeds into the same Livewire component as `/catalog`.

### updating() — reactive side effects
```php
public function updating($name, $value)
{
    // When category changes, reset all sub-filters
    if ($name === 'selectedCategory') {
        $this->selectedFactions = [];
        $this->selectedTypes    = [];
        $this->selectedColors   = [];
        $this->selectedGenres   = [];
        $this->selectedPlayers  = [];
    }

    // Reset to page 1 whenever any filter changes
    if (in_array($name, ['search', 'selectedCategory', 'selectedFactions',
                          'selectedTypes', 'selectedColors', 'selectedGenres',
                          'selectedPlayers', 'sort'])) {
        $this->resetPage();
    }
}
```
Without `resetPage()`, changing a filter while on page 3 would show page 3 of the new filtered results — which might have fewer pages and show an empty result.

### render() — building the dynamic query
```php
public function render()
{
    $query = Product::query();

    // 1. Text search
    if (!empty($this->search)) {
        $query->where('name', 'like', '%' . $this->search . '%');
    }

    // 2. Category filter
    if (!empty($this->selectedCategory)) {
        $query->where('category_id', $this->selectedCategory);
    }

    // 3. Faction filter (figurines) — OR within the group
    if (!empty($this->selectedFactions)) {
        $query->where(function ($q) {
            foreach ($this->selectedFactions as $faction) {
                $q->orWhereJsonContains('attributes->faction', $faction);
            }
        });
    }

    // (same pattern for selectedTypes, selectedColors, selectedGenres, selectedPlayers)

    // 4. Sorting
    switch ($this->sort) {
        case 'price_asc':  $query->orderBy('price', 'asc'); break;
        case 'price_desc': $query->orderBy('price', 'desc'); break;
        default:           $query->orderBy('created_at', 'desc');
    }

    $products = $query->paginate(24);
```

#### JSON attribute filtering explained
```php
// MySQL JSON_CONTAINS query generated by orWhereJsonContains:
// SELECT * FROM products
// WHERE JSON_CONTAINS(attributes, '"Space Marines"', '$.faction')
//    OR JSON_CONTAINS(attributes, '"Chaos Space Marines"', '$.faction')
```
This queries inside the JSON `attributes` column without needing to decode the whole column in PHP.

#### Dynamic filter options
```php
    // Fetch all distinct faction values currently in the DB
    $factions = Product::whereNotNull('attributes->faction')
        ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.faction')) as faction"))
        ->distinct()
        ->pluck('faction')
        ->filter();  // remove nulls/empty strings
```
The `JSON_EXTRACT` + `JSON_UNQUOTE` combo extracts the value of `$.faction` from each row's JSON and strips the surrounding quotes. `->distinct()` deduplicates, giving us only the values that actually exist in the DB — no hardcoded filter lists.

The same pattern applies for `$types`, `$colors`, `$genres`, `$players`.

```php
    // Determine active category slug (used in view to show/hide filter panels)
    $activeCategory = $this->selectedCategory
        ? Category::find($this->selectedCategory)?->slug
        : null;

    return view('livewire.shop.catalog', [
        'products'         => $products,
        'categories'       => $categories,
        'availableFactions'=> $factions,
        'availableTypes'   => $types,
        'availableColors'  => $colors,
        'availableGenres'  => $genres,
        'availablePlayers' => $players,
        'activeCategory'   => $activeCategory,
    ])->layout('layouts.tenant', ['title' => 'Catalog | Adept\'s Armoury']);
    // layout() sets the full-page layout for this Livewire component
}
```

### Catalog view (`resources/views/livewire/shop/catalog.blade.php`)

#### Search input (debounced)
```html
<input wire:model.live.debounce.300ms="search"
       type="text"
       placeholder="Search relics..."
       class="...">
```
`wire:model.live` — updates the Livewire property on every keystroke.
`.debounce.300ms` — waits 300ms after the last keystroke before triggering the server request. Prevents a request on every single character.

#### Category-specific filter panels
```blade
{{-- Only show Faction filter when browsing Figurines --}}
@if(count($availableFactions) > 0 && $activeCategory === 'figurines')
<div class="bg-slate-900/60 ... rounded-2xl p-5">
    <h3 class="... font-cinzel">Faction</h3>
    @foreach($availableFactions as $faction)
        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox"
                   wire:model.live="selectedFactions"
                   value="{{ $faction }}"
                   class="peer sr-only">
            {{-- Custom styled checkbox using Tailwind peer classes --}}
            <div class="w-4 h-4 border border-white/20 rounded
                        peer-checked:bg-yellow-500
                        peer-checked:border-yellow-500 transition-all">
            </div>
            <span class="text-sm text-slate-400 group-hover:text-slate-200">
                {{ $faction }}
            </span>
        </label>
    @endforeach
</div>
@endif
```
`peer sr-only` hides the actual `<input>` visually but keeps it accessible. The styled `<div>` next to it uses `peer-checked:` classes to visually reflect the checkbox state.

#### Loading overlay
```blade
<div wire:loading class="absolute inset-0 z-20 bg-slate-950/50 backdrop-blur-sm
                          rounded-xl flex items-center justify-center">
    <div class="w-10 h-10 border-4 border-yellow-500/20
                border-t-yellow-500 rounded-full animate-spin">
    </div>
</div>
```
`wire:loading` shows this element only while a Livewire request is in flight. Gives visual feedback during filtering.

#### Sort dropdown
```blade
<select wire:model.live="sort" id="sort" class="...">
    <option value="newest">Newest Arrivals</option>
    <option value="price_asc">Price: Low to High</option>
    <option value="price_desc">Price: High to Low</option>
</select>
```
`wire:model.live` — triggers a re-render immediately on change (no debounce needed since it's a select, not a text field).

#### Product grid with pagination
```blade
@if($products->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($products as $product)
            <x-product-card :product="$product" wire:key="product-{{ $product->id }}" />
        @endforeach
    </div>
    <div class="mt-12">
        {{ $products->links() }}  {{-- renders Tailwind pagination links --}}
    </div>
@else
    {{-- Empty state --}}
    <div class="text-center py-24 ...">
        <p class="text-slate-400 font-cinzel text-xl">No relics match your criteria.</p>
        <button wire:click="$set('search', ''); $set('selectedCategories', []);">
            Clear Filters
        </button>
    </div>
@endif
```
`wire:key` gives each card a stable identity so Livewire can efficiently diff/patch the DOM during re-renders. Without it, Livewire might re-render all cards even when only one changes.


---

## 9. Middleware

### TenantAdmin (`app/Http/Middleware/TenantAdmin.php`)
```php
class TenantAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
```
Applied to all protected admin routes. Checks:
1. `auth()->check()` — is there a logged-in session at all?
2. `auth()->user()->is_admin` — does that user have the admin flag?

Both conditions must be true. If either fails, redirect to admin login.

Note: This checks the `is_admin` column on the tenant `users` table, NOT the central `admins` table. This is why the admin login controller manually logs in via the default `auth` guard (which queries tenant users), then checks `is_admin`.

### Stancl Tenancy Middleware (in routes/tenant.php)
```php
Route::middleware([
    'web',                              // sessions, CSRF, cookies
    InitializeTenancyByDomain::class,  // switches DB to tenant's database
    PreventAccessFromCentralDomains::class, // 404 if on central domain
])->group(...)
```
**`InitializeTenancyByDomain`**: Reads `$request->getHost()`, queries `domains` table in central DB, retrieves tenant, calls `tenancy()->initialize($tenant)` which swaps the database connection. After this, all Eloquent queries go to the tenant's DB.

**`PreventAccessFromCentralDomains`**: Returns 404 if the current host is a configured central domain. Ensures storefront routes are only accessible on tenant domains.

---

## 10. Blade Layouts

### Tenant Layout (`resources/views/layouts/tenant.blade.php`)

Used by all storefront pages. Livewire Catalog uses it via `.layout('layouts.tenant')`.

#### Head section
```html
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Warhammer 40k Shop' }}</title>

    <!-- Fonts from Bunny Fonts CDN (privacy-friendly Google Fonts alternative) -->
    <link href="https://fonts.bunny.net/css?family=cinzel:400,600,700,800|outfit:300,400,500,600,700&display=swap"
          rel="stylesheet" />

    <!-- Vite-compiled CSS and JS (Tailwind + Alpine/Livewire) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body    { font-family: 'Outfit', sans-serif; background-color: #0f172a; }
        h1,h2,h3,h4,h5,h6,.font-cinzel { font-family: 'Cinzel', serif; }
        .wh-border  { border: 1px solid rgba(234,179,8,0.2); }     /* gold 20% opacity */
        .wh-bg-card { background-color: rgba(15,23,42,0.6);         /* glassmorphism */
                      backdrop-filter: blur(12px); }
    </style>
</head>
```
The `$title` variable is passed from individual views/components via the slot system or Livewire's `.layout()` call.

#### Background layers
```html
<body class="antialiased min-h-screen flex flex-col bg-slate-900
             bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">

    <!-- Atmospheric gradient overlay (fixed, behind everything) -->
    <div class="fixed inset-0 z-[-1]
                bg-gradient-to-br from-indigo-900/20 via-slate-900 to-yellow-900/10
                pointer-events-none">
    </div>
```
Three layers:
1. Base dark slate body (`bg-slate-900`)
2. Texture pattern overlay (dark matter pattern PNG)
3. Fixed gradient from indigo (purple) to yellow for an ominous atmosphere

#### Sticky header with dropdowns
```html
<header class="sticky top-0 z-50 bg-slate-950/70 backdrop-blur-md
               border-b border-yellow-500/20
               shadow-[0_4px_30px_rgba(0,0,0,0.5)] transition-all">
```
`backdrop-blur-md` — the header blurs what's behind it (glassmorphism). Only works if the body has content behind it, which it does since `sticky` keeps it in the flow.

#### Department dropdown (CSS-only hover)
```html
<div class="relative group">
    <a href="{{ route('shop.catalog') }}" class="... focus:outline-none py-2 relative">
        Departments
        <svg class="... transform group-hover:rotate-180 transition-transform duration-300">...</svg>
    </a>

    <!-- Dropdown: invisible by default, visible on group hover -->
    <div class="absolute left-0 top-full pt-4 w-56
                opacity-0 invisible
                group-hover:opacity-100 group-hover:visible
                transition-all duration-300
                translate-y-2 group-hover:translate-y-0 z-50">
        <div class="bg-slate-900/95 backdrop-blur-xl border border-yellow-500/20 rounded-xl ...">
            <a href="{{ route('shop.category', 'paints') }}" class="block px-5 py-3.5 ...">Paints</a>
            ...
        </div>
    </div>
</div>
```
Pure CSS hover via Tailwind's `group`/`group-hover:` — no JS needed. The `translate-y` transition creates a subtle slide-down effect.

#### Auth-aware header actions
```blade
@auth
    {{-- Logged in: show username + dropdown with profile/admin/logout --}}
    <div class="relative group">
        <button class="flex items-center gap-2 ...">
            {{ auth()->user()->name }}
        </button>
        <div class="absolute right-0 top-full ... group-hover:opacity-100">
            <a href="{{ route('shop.profile') }}">Dossier / Profile</a>
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}">Administratum</a>
            @endif
            <form method="POST" action="{{ route('shop.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
@else
    {{-- Guest: show login button --}}
    <a href="{{ route('shop.login') }}">Log In</a>
@endauth
```

#### Cart button with live badge
```blade
<a href="{{ route('shop.cart.index') }}" class="relative ...">
    Cart
    @if(session()->has('cart') && count(session('cart')) > 0)
        <span class="absolute -top-1 -right-1 bg-red-600 ... rounded-full w-5 h-5">
            {{ count(session('cart')) }}
        </span>
    @endif
</a>
```
Shows number of distinct product lines in cart (not total quantity). Reads directly from the session on every page render.

#### Flash message banners
```blade
@if(session('success'))
<div class="bg-emerald-900/80 backdrop-blur-md border-b border-emerald-500
            text-emerald-100 px-4 py-3 text-center text-sm font-semibold
            shadow-lg animate-fade-in-down">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-900/80 ...">{{ session('error') }}</div>
@endif
```
Positioned between header and main content. Uses `animate-fade-in-down` (custom Tailwind animation) for a subtle entrance.

---

### Admin Layout (`resources/views/components/admin-layout.blade.php`)
Used as an anonymous component `<x-admin-layout>` in all admin views.

```html
<body class="antialiased min-h-screen bg-slate-900 text-slate-200 flex">

    <!-- Sticky sidebar navigation -->
    <aside class="w-64 bg-slate-950 border-r border-slate-800 flex flex-col h-screen sticky top-0">
        <div class="h-20 flex items-center px-6 border-b border-slate-800">
            <h1 class="text-xl font-bold text-yellow-500 uppercase">Administratum</h1>
        </div>

        <nav class="flex-grow p-4 space-y-2">
            <!-- Active route highlighting with request()->routeIs() -->
            <a href="{{ route('admin.dashboard') }}"
               class="block px-4 py-3 rounded-lg
                      {{ request()->routeIs('admin.dashboard')
                         ? 'bg-slate-800 text-yellow-500 font-bold'
                         : 'text-slate-400 hover:bg-slate-800' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="{{ request()->routeIs('admin.categories.*') ? 'bg-slate-800 text-yellow-500 font-bold' : 'text-slate-400' }} ...">
                Categories
            </a>
            <!-- Products, Orders links follow same pattern -->
        </nav>

        <div class="p-4 border-t border-slate-800">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="... text-red-400">Log out</button>
            </form>
        </div>
    </aside>

    <!-- Main content area -->
    <div class="flex-grow flex flex-col min-h-screen">
        <header class="h-20 bg-slate-950 ... sticky top-0 z-10">
            Logged in as <span class="text-yellow-500">{{ auth()->user()->name }}</span>
            <a href="{{ route('shop.home') }}">View Shop</a>
        </header>

        <main class="flex-grow p-8 max-w-7xl mx-auto w-full">
            @if(session('success'))
            <div class="mb-6 bg-emerald-900/50 border border-emerald-500 ...">
                {{ session('success') }}
            </div>
            @endif

            {{ $slot }}  {{-- Page content goes here --}}
        </main>
    </div>
</body>
```

`request()->routeIs('admin.categories.*')` — matches any route name starting with `admin.categories.`, so the sidebar link stays highlighted on the create/edit pages too.


---

## 11. Storefront Views

### `shop/home.blade.php`

```blade
<x-tenant-layout title="Home | Adept's Armoury">

    {{-- Hero section with layered background effects --}}
    <div class="mb-16 text-center relative py-20 overflow-hidden
                rounded-3xl bg-slate-900/60 backdrop-blur-md border border-yellow-500/10">
        {{-- Decorative blurred circles (light leak effect) --}}
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-yellow-600/20 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-[100px]"></div>

        <h1 class="text-5xl md:text-7xl font-cinzel font-black
                   text-transparent bg-clip-text
                   bg-gradient-to-br from-yellow-300 via-yellow-500 to-amber-700">
            Welcome to Adept's Armoury
        </h1>

        {{-- Redirects to ShopController@home which forwards to Livewire catalog --}}
        <form action="{{ route('shop.home') }}" method="GET">
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
    </div>

    @if(isset($products))
        {{-- Search results mode: $products is set by controller when search query present --}}
        @foreach($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    @else
        {{-- Homepage mode: category carousels --}}
        @foreach($categorySections as $category)
        <section>
            <h2>Top {{ $category->name }}</h2>
            <a href="{{ route('shop.category', $category->slug) }}">View all</a>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($category->products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
        @endforeach
    @endif
</x-tenant-layout>
```
The view handles two states based on whether `$products` is set by the controller (search results) or not (category carousels). The controller passes `$categorySections` with eager-loaded products (`take(4)` newest per category).

---

### `shop/product.blade.php` — Product Detail Page

Key sections:

#### Smart back button
```blade
<a href="{{ url()->previous() == url()->current() ? route('shop.home') : url()->previous() }}">
    Return
</a>
```
If the user navigated directly (previous URL = current URL), send them home. Otherwise send them back. Prevents broken "back" on direct links.

#### Stock display with per-store breakdown
```blade
{{-- Online stock from central warehouse --}}
@if($product->stock > 0)
    <span class="text-emerald-500">In Stock ({{ $product->stock }})</span>
@else
    <span class="text-red-500">Depleted</span>
@endif

{{-- Physical store breakdown (non-warehouse locations) --}}
@if($product->productStocks->where('physicalStore.is_central_warehouse', false)->count() > 0)
    @foreach($product->productStocks->where('physicalStore.is_central_warehouse', false) as $stockItem)
        <li>
            {{ $stockItem->physicalStore->name }} ({{ $stockItem->physicalStore->city }})
            — {{ $stockItem->quantity > 0 ? $stockItem->quantity . ' in stock' : 'Out of stock' }}
        </li>
    @endforeach
@endif
```
Uses the already-eager-loaded `productStocks.physicalStore` relationship. Filters in PHP using Eloquent collection methods (no additional DB query).

#### Add to cart with quantity picker
```blade
<form action="{{ route('shop.cart.add', $product->id) }}" method="POST">
    @csrf
    <input type="number" name="quantity" value="1" min="1"
           max="{{ $product->stock > 0 ? $product->stock : 1 }}"
           @if($product->stock <= 0) disabled @endif>

    <button type="submit"
            @if($product->stock <= 0) disabled @endif
            class="... disabled:opacity-50 disabled:cursor-not-allowed">
        Add to Cart
    </button>
</form>
```
`max="{{ $product->stock }}"` prevents adding more than available. Both input and button are disabled when out of stock.

#### Review section
```blade
{{-- Rating summary card --}}
<div class="text-5xl font-bold text-yellow-500">{{ number_format($product->average_rating, 1) }}</div>
{{-- 5-star visual --}}
@for($i = 1; $i <= 5; $i++)
    <svg class="{{ $i <= round($product->average_rating) ? 'text-yellow-500' : 'text-slate-700' }}">
        {{-- star SVG path --}}
    </svg>
@endfor

{{-- Write a review (auth only) --}}
@auth
    <form action="{{ route('shop.product.review', $product->id) }}" method="POST">
        @csrf
        <select name="rating">
            <option value="5">5 Stars - Flawless</option>
            <option value="4">4 Stars - Great</option>
            <option value="3">3 Stars - Average</option>
            <option value="2">2 Stars - Poor</option>
            <option value="1">1 Star - Heresy</option>
        </select>
        <textarea name="comment" required></textarea>
        <button type="submit">Submit Protocol</button>
    </form>
@else
    <a href="{{ route('shop.login') }}">Log In to review</a>
@endauth

{{-- Existing reviews list --}}
@forelse($product->reviews()->latest()->get() as $review)
    <div>
        {{-- Avatar from first 2 letters of username --}}
        <div>{{ substr($review->user->name, 0, 2) }}</div>
        <div>{{ $review->user->name }}</div>
        <div>{{ $review->created_at->diffForHumans() }}</div>  {{-- "3 days ago" --}}
        <p>{{ $review->comment }}</p>
    </div>
@empty
    <p>No reviews yet.</p>
@endforelse
```

---

### `shop/cart.blade.php`

```blade
<x-tenant-layout title="Your Cart | Adept's Armoury">
@if($products->count() > 0)
    {{-- Cart items (2/3 width) --}}
    @foreach($products as $product)
        {{-- SKU generated from ID: RELIC-0042 --}}
        <div>SKU: RELIC-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</div>

        {{-- Line total: price × quantity (both in cents, divide for display) --}}
        €{{ number_format(($product->price * $product->cart_quantity) / 100, 2) }}

        {{-- Remove button (POST form for CSRF safety) --}}
        <form action="{{ route('shop.cart.remove', $product->id) }}" method="POST">
            @csrf
            <button type="submit">Remove</button>
        </form>
    @endforeach

    {{-- Order summary sidebar (1/3 width, sticky) --}}
    <div class="sticky top-6">
        Total: €{{ number_format($total / 100, 2) }}
        <a href="{{ route('shop.checkout.index') }}">Proceed to Checkout</a>
    </div>

@else
    {{-- Empty cart state --}}
    <div class="text-center py-24">
        <h2>Your Requisition is Empty</h2>
        <a href="{{ route('shop.home') }}">Visit Armory</a>
    </div>
@endif
</x-tenant-layout>
```

---

### `shop/checkout.blade.php`

```blade
<x-tenant-layout title="Checkout | Adept's Armoury">
<div class="flex flex-col-reverse lg:flex-row gap-8">

    {{-- Checkout form (2/3 width) --}}
    <form action="{{ route('shop.checkout.store') }}" method="POST">
        @csrf
        {{-- Name + email --}}
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name') <span>{{ $message }}</span> @enderror

        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <span>{{ $message }}</span> @enderror

        {{-- Delivery address --}}
        <input type="text" name="address" value="{{ old('address') }}">
        <input type="text" name="city" value="{{ old('city') }}">
        <input type="text" name="postal_code" value="{{ old('postal_code') }}">

        <button type="submit">Confirm Order</button>
        {{-- This POSTs to CheckoutController@store which creates the Stripe session
             and immediately redirects to Stripe's hosted payment page --}}
    </form>

    {{-- Order summary sidebar (1/3 width, sticky) --}}
    <div class="sticky top-6">
        @foreach($products as $product)
            {{ $cart[$product->id] }}x {{ $product->name }}
            €{{ number_format(($product->price * $cart[$product->id]) / 100, 2) }}
        @endforeach
        Total: €{{ number_format($total / 100, 2) }}
    </div>

</div>
</x-tenant-layout>
```
`flex-col-reverse` on mobile puts the order summary above the form (better UX). On `lg:` it switches to side-by-side.


---

## 12. Admin Panel Views

### `admin/dashboard.blade.php`
```blade
<x-admin-layout title="Dashboard">

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-slate-950 border border-slate-800 p-6 rounded-xl">
            <h3 class="text-slate-400 text-sm uppercase">Total Relics</h3>
            <div class="text-4xl font-bold text-slate-100">{{ $totalProducts }}</div>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-6 rounded-xl">
            <h3 class="text-slate-400 text-sm uppercase">Total Orders</h3>
            {{-- Direct model call in view (acceptable for simple counts) --}}
            <div class="text-4xl font-bold">{{ \App\Models\Order::count() }}</div>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-6 rounded-xl border-b-4 border-b-yellow-600">
            <h3 class="text-slate-400 text-sm uppercase">Revenue (Paid)</h3>
            <div class="text-4xl font-bold text-yellow-500">
                €{{ number_format($totalRevenue / 100, 2) }}
            </div>
        </div>
    </div>

    {{-- Recent orders table with status badges --}}
    <table>
        @forelse($recentOrders as $order)
        <tr>
            <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a></td>
            <td>{{ $order->user->name }}</td>
            <td>€{{ number_format($order->total_amount / 100, 2) }}</td>
            <td>
                {{-- Dynamic badge colors per status --}}
                <span class="px-2 py-1 rounded text-xs font-bold
                    {{ $order->status === 'paid'      ? 'bg-emerald-900/50 text-emerald-400' : '' }}
                    {{ $order->status === 'pending'   ? 'bg-amber-900/50 text-amber-400' : '' }}
                    {{ $order->status === 'shipped' || $order->status === 'delivered'
                                                      ? 'bg-blue-900/50 text-blue-400' : '' }}
                    {{ $order->status === 'cancelled' ? 'bg-red-900/50 text-red-400' : '' }}">
                    {{ $order->status }}
                </span>
            </td>
            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="5">No requisitions found.</td></tr>
        @endforelse
    </table>

</x-admin-layout>
```

---

### `admin/products/index.blade.php`
```blade
<x-admin-layout title="Products">
    <div class="flex items-center justify-between mb-8">
        <h1>Products (Relics)</h1>
        <a href="{{ route('admin.products.create') }}">+ New</a>
    </div>

    {{-- Filter form: name search + category dropdown --}}
    <form action="{{ route('admin.products.index') }}" method="GET"
          class="grid grid-cols-12 gap-4">
        <input type="text" name="search" value="{{ request('search') }}"
               class="col-span-7" placeholder="Search by name...">

        <select name="category_id" class="col-span-3">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="col-span-1">Filter</button>

        {{-- Show Clear button only when filters are active --}}
        @if(request()->anyFilled(['search', 'category_id']))
            <a href="{{ route('admin.products.index') }}" class="col-span-1">Clear</a>
        @endif
    </form>

    {{-- Products table --}}
    <table>
        <thead>...</thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category?->name ?? 'N/A' }}</td>
                <td>€{{ number_format($product->price / 100, 2) }}</td>
                <td>
                    {{-- Stock badge: green if > 0, red if 0 --}}
                    <span class="{{ $product->stock > 0
                        ? 'bg-emerald-900/50 text-emerald-400'
                        : 'bg-red-900/50 text-red-400' }}">
                        {{ $product->stock }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.products.edit', $product) }}">Edit</a>
                    {{-- DELETE via hidden _method field (HTML forms only support GET/POST) --}}
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                          onsubmit="return confirm('Delete this relic?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
                <tr><td colspan="6">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($products->hasPages())
        {{ $products->links() }}
    @endif
</x-admin-layout>
```
`@method('DELETE')` outputs `<input type="hidden" name="_method" value="DELETE">`. Laravel's router reads this to treat the POST as a DELETE request, since HTML forms cannot send DELETE natively.

---

## 13. Stripe Payment Flow

### Complete sequence diagram
```
Customer fills /checkout form (name, email, address)
            │
            ▼
POST /checkout → CheckoutController@store
  1. Read cart from session
  2. Fetch products from DB
  3. Build Stripe line_items array
  4. DB::beginTransaction()
  5.   User::firstOrCreate(email)
  6.   Order::create(status: 'pending')
  7.   OrderItem::create() × N
  8. Stripe\Session::create([
         mode: 'payment',
         line_items: [...],
         metadata: { order_id, tenant_id },
         success_url: /checkout/success?session_id={CHECKOUT_SESSION_ID},
         cancel_url: /checkout/cancel
     ])
  9. Order->update(stripe_session_id)
  10. DB::commit()
  11. redirect($checkoutSession->url)
            │
            ▼
Stripe Hosted Payment Page (stripe.com)
  - Handles card input, 3DS, fraud detection
  - User pays
            │
     ┌──────┴──────────────────────────────────┐
     │                                          │
     ▼                                          ▼
GET /checkout/success                  POST /stripe/webhook
?session_id=cs_xxx                     (Stripe server → our server)
            │                                  │
CheckoutController@success         StripeWebhookController@handleWebhook
  find Order by stripe_session_id     1. Verify HMAC signature
  if status == pending:               2. Parse event type
    update status = paid              3. checkout.session.completed:
    session()->forget('cart')            find order by metadata.order_id
    redirect to home                     if pending: update to paid
```

### Environment variables required
```env
STRIPE_KEY=pk_test_...          # Publishable key (frontend, not used here)
STRIPE_SECRET=sk_test_...       # Secret key (server-side API calls)
STRIPE_WEBHOOK_SECRET=whsec_... # Webhook signing secret (signature verification)
```

### Why both success redirect AND webhook?
| | Success Redirect | Webhook |
|---|---|---|
| **Triggered by** | Stripe redirecting user's browser | Stripe's servers POST to your URL |
| **Can fail if** | User closes tab, bad connection | Extremely reliable |
| **Use case** | Immediate UX feedback | Authoritative payment confirmation |
| **Idempotent?** | ✅ (checks `status === 'pending'`) | ✅ (same check) |

Both update the order to `paid` only if it's still `pending`, so whichever fires first wins, and the second is a harmless no-op.

### Refund flow (admin OrderController)
```
Admin clicks "Cancel" on a paid/shipped/delivered order
            │
            ▼
PATCH /dashboard/orders/{id}/status
  { status: 'cancelled' }
            │
Admin\OrderController@updateStatus
  if newStatus == 'cancelled'
  AND oldStatus in [paid, shipped, delivered]
  AND order->stripe_session_id exists:
    │
    ├─ Session::retrieve(stripe_session_id)
    │    → get payment_intent ID from Checkout Session
    │
    ├─ Refund::create([payment_intent => $id])
    │    → Stripe processes full refund to customer's card
    │
    ├─ If refund throws: return error, DON'T change status
    │
    └─ If refund succeeds: update order status to 'cancelled'
```

---

## 14. Design System

### Color palette
| Token | Value | Usage |
|---|---|---|
| `slate-950` | `#020617` | Admin backgrounds, deepest darks |
| `slate-900` | `#0f172a` | Page background |
| `slate-800` | `#1e293b` | Card backgrounds |
| `yellow-500` | `#eab308` | Primary accent (gold) |
| `yellow-600` | `#ca8a04` | Darker gold for buttons |
| `emerald-*` | green family | Success states, in-stock |
| `red-*` | red family | Errors, out-of-stock, delete |
| `indigo-900/20` | indigo tint | Background gradient |

### Typography
```css
/* Headings — all h1–h6 and .font-cinzel */
font-family: 'Cinzel', serif;      /* Roman/gothic letterforms */

/* Body — all other text */
font-family: 'Outfit', sans-serif; /* Modern, clean geometric */
```

### Reusable CSS utility classes (inline styles in tenant layout)
```css
.wh-border  { border: 1px solid rgba(234, 179, 8, 0.2); }
/* Semi-transparent gold border — used on cards */

.wh-bg-card {
    background-color: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(12px);
}
/* Glassmorphism card style — frosted glass on dark background */
```

### Component patterns

#### Glassmorphism card
```html
<div class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-2xl">
```

#### Glow button (primary action)
```html
<button class="bg-yellow-600 hover:bg-yellow-500 text-slate-950 font-bold
               shadow-[0_0_15px_rgba(234,179,8,0.2)]
               hover:shadow-[0_0_20px_rgba(234,179,8,0.5)]
               transition-all duration-300">
```
The `shadow-[]` arbitrary values use Tailwind's JIT to produce a yellow glow that intensifies on hover.

#### Hover lift + glow (product card)
```html
<div class="hover:shadow-[0_0_30px_rgba(234,179,8,0.15)]
            hover:border-yellow-500/30
            hover:-translate-y-1
            transition-all duration-500">
```

#### Custom scrollbar (filter panels)
```css
.custom-scrollbar  /* applied to overflow containers in catalog sidebar */
/* defined in app.css to style the scrollbar thumb to match the dark theme */
```

---

*Documentation generated 2026-06-24. Covers: routes/, app/Models/, app/Http/Controllers/, app/Livewire/, app/Http/Middleware/, resources/views/ (layouts, components, shop/*, admin/*, livewire/shop/).*
