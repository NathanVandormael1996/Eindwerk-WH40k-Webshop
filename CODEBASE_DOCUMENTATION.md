# Adept's Armoury — Codebase Documentation

> A multi-tenant Warhammer 40,000 e-commerce platform built with **Laravel 13**, **Livewire 4**, **Stancl Tenancy**, and **Stripe**.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack & Dependencies](#2-tech-stack--dependencies)
3. [Architecture: Multi-Tenancy](#3-architecture-multi-tenancy)
4. [Routing](#4-routing)
5. [Database & Migrations](#5-database--migrations)
6. [Models](#6-models)
7. [Controllers](#7-controllers)
8. [Livewire Components](#8-livewire-components)
9. [Middleware](#9-middleware)
10. [Blade Views & Layouts](#10-blade-views--layouts)
11. [Payment Flow (Stripe)](#11-payment-flow-stripe)
12. [Data Flow Diagrams](#12-data-flow-diagrams)

---

## 1. Project Overview

**Adept's Armoury** is a themed Warhammer 40K webshop. The project uses a **multi-tenant** architecture, meaning the same codebase can power multiple independent shops — each with its own database, domain, and branding.

Each tenant (shop) has:
- Their own isolated database (all product, order, user data is per-tenant)
- A custom domain routed via Stancl Tenancy
- A shared central database for global config (tenants, domains, countries, admins)

The shop sells six categories of products:
- **Paints** — hobby paints, filterable by color
- **Figurines** — miniatures, filterable by faction (Space Marines, Chaos, etc.)
- **Videogames** — games, filterable by genre
- **Boardgames** — tabletop games, filterable by player count
- **Apparel** — clothing, filterable by type
- **Comics** — books and comics

---

## 2. Tech Stack & Dependencies

### PHP / Backend
| Package | Version | Purpose |
|---|---|---|
| `laravel/framework` | ^13.7 | Core framework |
| `livewire/livewire` | ^4.0 | Reactive UI components |
| `livewire/flux` | ^2.14 | Livewire UI component library |
| `livewire/volt` | ^1.7.0 | Single-file Livewire components |
| `stancl/tenancy` | ^3.10 | Multi-tenancy (per-domain DB isolation) |
| `stripe/stripe-php` | ^20.2 | Payment processing |

### Dev Dependencies
| Package | Purpose |
|---|---|
| `laravel/breeze` | Auth scaffolding (central domain) |
| `pestphp/pest` | Testing framework |
| `laravel/pint` | Code style fixer |
| `laravel/pail` | Log tailing in dev |

### Frontend
- **Tailwind CSS** — utility-first styling
- **Vite** — asset bundling
- **Cinzel** (Google Fonts) — heading font, gothic/roman style fitting the WH40K theme
- **Outfit** (Google Fonts) — body font, clean and modern

---

## 3. Architecture: Multi-Tenancy

The application uses the **Stancl Tenancy** package to run multiple independent shops from one codebase.

### How It Works

```
Central Domain (e.g. admin.example.com)
  └── Central DB: tenants, domains, countries, admins (global admins)

Tenant Domain (e.g. shop1.example.com)
  └── Tenant DB: users, products, categories, orders, reviews, stocks
```

- **Central domain**: Handles platform-level admin (managing which shops exist). Uses `routes/web.php`.
- **Tenant domains**: Each shop runs on its own subdomain. Uses `routes/tenant.php`. Every request to a tenant domain is intercepted by two middleware:
  - `InitializeTenancyByDomain` — reads the domain, switches DB connection to that tenant's database.
  - `PreventAccessFromCentralDomains` — ensures storefront routes can't be accessed from the central domain.

### The `Shop` Model (`app/Models/Shop.php`)

The `Shop` model *is* the tenant. It extends `BaseTenant` from Stancl and implements `TenantWithDatabase`. Custom columns stored in the central `tenants` table:

```php
public static function getCustomColumns(): array
{
    return ['id', 'country_id', 'name', 'currency'];
}
```

Each `Shop` links to a `Country` and stores its preferred currency (default: EUR).

---

## 4. Routing

### Central Domain — `routes/web.php`

Wraps all routes inside a `foreach` loop over configured central domains. Only exposes:
- `GET /` → redirects to `/login`
- `GET /dashboard` → central admin dashboard (auth + verified)
- `GET /profile` → user profile (auth required)
- Auth routes from `routes/auth.php` (Breeze-generated)

### Tenant Domain — `routes/tenant.php`

All routes are wrapped in the tenancy middleware group. Broken into logical sections:

#### Storefront Routes
| Method | URI | Handler | Route Name |
|---|---|---|---|
| GET | `/` | `ShopController@home` | `shop.home` |
| GET | `/catalog` | `Catalog` (Livewire) | `shop.catalog` |
| GET | `/collections/{slug}` | `Catalog` (Livewire) | `shop.category` |
| GET | `/products/{slug}` | `ShopController@product` | `shop.product` |

#### Cart Routes (prefix: `/cart`)
| Method | URI | Handler | Route Name |
|---|---|---|---|
| GET | `/cart` | `CartController@index` | `shop.cart.index` |
| POST | `/cart/add/{product}` | `CartController@add` | `shop.cart.add` |
| POST | `/cart/remove/{product}` | `CartController@remove` | `shop.cart.remove` |

#### Checkout Routes (prefix: `/checkout`)
| Method | URI | Handler | Route Name |
|---|---|---|---|
| GET | `/checkout` | `CheckoutController@index` | `shop.checkout.index` |
| POST | `/checkout` | `CheckoutController@store` | `shop.checkout.store` |
| GET | `/checkout/success` | `CheckoutController@success` | `shop.checkout.success` |
| GET | `/checkout/cancel` | `CheckoutController@cancel` | `shop.checkout.cancel` |
| POST | `/stripe/webhook` | `StripeWebhookController@handleWebhook` | `stripe.webhook` |

#### Customer Auth Routes
| Method | URI | Handler | Route Name |
|---|---|---|---|
| GET | `/login` | `Auth\LoginController@showLoginForm` | `shop.login` |
| POST | `/login` | `Auth\LoginController@login` | — |
| GET | `/register` | `Auth\RegisterController@showRegistrationForm` | `shop.register` |
| POST | `/register` | `Auth\RegisterController@register` | — |
| POST | `/logout` | `Auth\LoginController@logout` | `shop.logout` |
| GET | `/profile` | `Auth\ProfileController@index` | `shop.profile` |
| POST | `/products/{product}/review` | `ShopController@storeReview` | `shop.product.review` |

#### Admin Routes (prefix: `/dashboard`)
| Method | URI | Handler | Route Name |
|---|---|---|---|
| GET | `/dashboard/login` | `Admin\LoginController@showLoginForm` | `admin.login` |
| POST | `/dashboard/login` | `Admin\LoginController@login` | — |
| POST | `/dashboard/logout` | `Admin\LoginController@logout` | `admin.logout` |
| GET | `/dashboard` | `Admin\DashboardController@index` | `admin.dashboard` |
| Resource | `/dashboard/categories` | `Admin\CategoryController` | `admin.categories.*` |
| Resource | `/dashboard/products` | `Admin\ProductController` | `admin.products.*` |
| GET | `/dashboard/orders` | `Admin\OrderController@index` | `admin.orders.index` |
| GET | `/dashboard/orders/{order}` | `Admin\OrderController@show` | `admin.orders.show` |
| PATCH | `/dashboard/orders/{order}/status` | `Admin\OrderController@updateStatus` | `admin.orders.updateStatus` |

---

## 5. Database & Migrations

### Central Database Tables

#### `countries`
Stores country codes — referenced by tenants to know which country a shop belongs to.

#### `tenants`
The core multi-tenancy table managed by Stancl. Columns:
- `id` (string, primary key — the tenant identifier)
- `country_id` (FK to countries)
- `name` — human-readable shop name
- `currency` — default `EUR`
- `data` (JSON) — arbitrary extra metadata

#### `domains`
Maps domain names to tenant IDs. Stancl uses this to route incoming requests.

#### `admins`
Central/global admin accounts. Columns:
- `id`, `name`, `email`, `password`, `role`
- These admins exist in the central database and are *not* per-tenant.

#### `sessions`
Standard Laravel session storage table.

---

### Tenant Database Tables (per-shop, in `database/migrations/tenant/`)

Each tenant gets their own copy of these tables in an isolated database.

#### `users`
Standard Laravel users table (auth scaffolding). Columns include `name`, `email`, `password`, `email_verified_at`, `remember_token`.

Later migration `2026_06_15_...` adds extra user fields (e.g. address fields for orders).

#### `categories`
- `id`, `name`, `slug`, `timestamps`
- A slug is auto-generated from the name (e.g. "Paints" → `paints`)
- One-to-many with products

#### `products`
Core product table. Base columns:
- `id`, `category_id` (FK), `name`, `slug`, `description`, `price` (integer, stored in **cents**), `timestamps`

Additional columns added through later migrations:
- `image_url` — product image, typically a CDN URL
- `tags` (JSON array) — flexible tagging system
- `attributes` (JSON object) — category-specific attributes:
  - Figurines: `{ "faction": "Space Marines" }`
  - Paints: `{ "color": "Abaddon Black" }`
  - Videogames: `{ "genre": "Strategy" }`
  - Boardgames: `{ "players": "2-4" }`
  - Apparel: `{ "type": "T-Shirt" }`

> **Important**: Prices are stored as integers in cents (e.g., `2499` = €24.99). Always divide by 100 when displaying.

#### `orders`
- `id`, `user_id` (FK), `total_amount` (integer cents), `status`, `stripe_session_id`, `timestamps`
- Status values: `pending` → `paid` → `shipped` → `delivered` (or `cancelled`)

#### `order_items`
- `id`, `order_id` (FK), `product_id` (FK), `quantity`, `unit_price`, `timestamps`
- Stores a snapshot of the price at time of purchase

#### `reviews`
- `id`, `product_id` (FK), `user_id` (FK), `rating` (1-5), `comment`, `timestamps`

#### `physical_stores`
Represents brick-and-mortar store locations. Columns:
- `id`, `name`, `address`, `city`
- `is_central_warehouse` (boolean) — marks the main warehouse used for online stock calculations

#### `product_stocks`
Join table between products and physical stores:
- `id`, `product_id` (FK), `physical_store_id` (FK), `quantity`, `timestamps`
- Stock is tracked per-location, not as a single number on the product itself

---

## 6. Models

### `Shop` (`app/Models/Shop.php`)
Extends Stancl's `BaseTenant`. Represents a tenant/shop in the central database. Uses `HasDatabase` and `HasDomains` traits to manage the per-tenant DB and its domain mappings.

### `User` (`app/Models/User.php`)
Standard Laravel `Authenticatable`. Lives in the **tenant** database — each shop has its own set of customers. Uses PHP 8 attribute syntax (`#[Fillable]`, `#[Hidden]`) and casts `password` as hashed and `email_verified_at` as datetime.

### `Admin` (`app/Models/Admin.php`)
Lives in the **central** database. Also extends `Authenticatable`. Has a `role` field and `is_admin` flag. Used to authenticate the per-tenant admin panel. Password is auto-hashed via the `casts()` method.

### `Category` (`app/Models/Category.php`)
Simple model. Has a `products()` hasMany relationship back to `Product`.

### `Product` (`app/Models/Product.php`)
The most feature-rich model. Key aspects:

- `$guarded = []` — all fields are mass-assignable
- Casts: `tags` and `attributes` are automatically cast to/from JSON arrays
- **Relationships**:
  - `category()` — belongsTo Category
  - `reviews()` — hasMany Review
  - `productStocks()` — hasMany ProductStock
- **Computed attributes** (accessed like properties):
  - `$product->average_rating` — average of all review ratings, defaults to 0
  - `$product->stock` — sum of quantity only from stores where `is_central_warehouse = true`
  - `$product->total_stock` — sum of quantity across ALL physical stores

### `Order` (`app/Models/Order.php`)
- `user()` — belongsTo User
- `items()` — hasMany OrderItem

### `OrderItem` (`app/Models/OrderItem.php`)
Snapshot of a single line in an order. Belongs to both `Order` and `Product`.

### `Review` (`app/Models/Review.php`)
- `product()` — belongsTo Product
- `user()` — belongsTo User
- Fillable: `product_id`, `user_id`, `rating`, `comment`

### `PhysicalStore` (`app/Models/PhysicalStore.php`)
Represents a store location. Has `productStocks()` hasMany relationship.

### `ProductStock` (`app/Models/ProductStock.php`)
Join between `Product` and `PhysicalStore`. Has `product()` and `physicalStore()` belongsTo relationships.

---

## 7. Controllers

All tenant-facing controllers live in `app/Http/Controllers/Tenant/`.

---

### Storefront

#### `ShopController`
Handles the main public-facing shop pages.

**`home()`** — Checks for search/category/tag query parameters. If any filters are present, it redirects to the Catalog Livewire component. Otherwise it loads `Category::with(['products' => fn($q) => $q->latest()->take(4)])` to power the homepage category carousels, then returns `shop.home`.

**`category($slug)`** — Deprecated/legacy category route. Finds the category by slug, then builds a query with optional `tag` and `sort` filters, paginates 24 per page, and returns `shop.category`.

**`product($slug)`** — Loads a product by slug, eager-loading `reviews.user` and `productStocks.physicalStore` for the detail page. Also fetches 4 random related products from the same category.

**`storeReview(Request $request, Product $product)`** — Protected by `auth` middleware. Validates `rating` (1-5) and `comment` (max 1000 chars), then creates a `Review` record linked to the current user. Returns back with a success flash.

---

#### `CartController`
Manages the shopping cart, stored entirely in the **PHP session** (no database table).

The cart session key is `cart`, and its value is an array of `[product_id => quantity]`.

**`index()`** — Reads the cart from session. Fetches matching `Product` models, attaches `cart_quantity` dynamically to each product object, computes the total, and returns `shop.cart`.

**`add(Request $request, Product $product)`** — Reads cart from session. If the product already exists, increments quantity; otherwise adds it. Saves back to session. Redirects back with success.

**`remove(Product $product)`** — Unsets the product from the session cart array and saves. Redirects back with success.

---

#### `CheckoutController`
Handles the full Stripe Checkout flow.

**`index()`** — Reads cart from session. Redirects to cart if empty. Fetches products and computes total, returns `shop.checkout`.

**`store(StoreCheckoutRequest $request)`** — The core checkout action. Wrapped in a DB transaction:
1. Gets cart from session, fetches products, builds Stripe `line_items` array.
2. Uses `User::firstOrCreate()` — if the customer's email already exists, uses that account; otherwise creates a new one with a random password.
3. Creates an `Order` record with status `pending`.
4. Creates `OrderItem` records for each product in the cart.
5. Creates a Stripe Checkout Session with `mode: payment`, success/cancel URLs, and metadata containing the `order_id` and `tenant_id`.
6. Saves the `stripe_session_id` on the Order.
7. Commits the transaction and **redirects the user to Stripe's hosted checkout page**.
8. On any exception, rolls back the DB transaction and returns with an error.

**`success(Request $request)`** — Called after Stripe redirects back. Reads `session_id` from query string, finds the matching Order, sets status to `paid` if it was `pending`, clears the cart session, and redirects home with success.

**`cancel()`** — Simple redirect back to the cart with an error message.

---

#### `StripeWebhookController`
Handles Stripe's server-side webhook events (more reliable than the redirect-based success page).

**`handleWebhook(Request $request)`**:
1. Reads raw payload and `Stripe-Signature` header.
2. Verifies the webhook signature using `Webhook::constructEvent()` and `STRIPE_WEBHOOK_SECRET`. Returns 400 on failure.
3. If the event type is `checkout.session.completed`:
   - Extracts `order_id` from session metadata, falls back to looking up by `stripe_session_id`.
   - Sets order status to `paid` if it was still `pending`.
4. Returns a 200 JSON success response.

> This acts as a safety net for cases where the user closes the browser before the success redirect fires.

---

### Customer Auth

#### `Tenant\Auth\LoginController`
**`showLoginForm()`** — Returns `shop.auth.login` view.

**`login(Request $request)`** — Validates email + password. Calls `Auth::attempt()` with optional "remember me". On success, regenerates the session and redirects to the intended URL (or `shop.home`). On failure, returns back with a validation error.

**`logout(Request $request)`** — Logs out, invalidates session, regenerates CSRF token, redirects to `shop.home`.

---

#### `Tenant\Auth\RegisterController`
**`showRegistrationForm()`** — Returns `shop.auth.register` view.

**`register(Request $request)`** — Validates name, email (unique in tenant users table), and password (with confirmation + Laravel's default password rules). Creates a `User`, logs them in immediately, redirects to `shop.home`.

---

#### `Tenant\Auth\ProfileController`
**`index(Request $request)`** — Fetches all orders for the current user, eager-loading `items.product` for order history display. Returns `shop.auth.profile` with the user and their orders.

---

### Admin Panel

All admin controllers are under `app/Http/Controllers/Tenant/Admin/` and are protected by the `TenantAdmin` middleware.

#### `Admin\LoginController`
**`login(Request $request)`** — Validates credentials, calls `Auth::attempt()`. After success, checks `Auth::user()->is_admin` — if not admin, immediately logs them out and returns an error. This prevents regular shop customers from accessing the admin panel.

---

#### `Admin\DashboardController`
**`index()`** — Gathers three key metrics for the dashboard:
- `$totalProducts` — `Product::count()`
- `$recentOrders` — latest 5 orders with user eager-loaded
- `$totalRevenue` — sum of `total_amount` for all `paid` orders

Returns `admin.dashboard`.

---

#### `Admin\CategoryController`
Standard CRUD resource controller. Uses `Str::slug()` to auto-generate the slug from the category name on both `store` and `update`.

---

#### `Admin\ProductController`
Resource controller with search and filter support on `index()`.

**`index(Request $request)`** — Builds a query with optional `search` (name LIKE) and `category_id` filters. Paginates 20 per page, preserving query string. Returns `admin.products.index` with both products and all categories (for the filter dropdown).

**`store(Request $request)`** / **`update()`** — Validate name, category, description, price, stock. Auto-generate slug. Note: `stock` in this admin form writes directly to the product table; the more granular `product_stocks` system is separate.

---

#### `Admin\OrderController`
**`index()`** — All orders with user, latest first. Returns `admin.orders.index`.

**`show(Order $order)`** — Loads full order detail with `user` and `items.product`.

**`updateStatus(UpdateOrderStatusRequest $request, Order $order)`** — The most complex admin action. If the new status is `cancelled` and the old status was one of `paid`, `shipped`, or `delivered`, and the order has a `stripe_session_id`:
1. Sets Stripe API key.
2. Retrieves the Checkout Session from Stripe to get the `payment_intent` ID.
3. Creates a full `Refund` for the payment intent.
4. Appends a refund confirmation to the success message.
If Stripe refund fails, it returns early with an error (does **not** change the order status). Otherwise updates status and redirects.

---

## 8. Livewire Components

### `App\Livewire\Shop\Catalog` (`app/Livewire/Shop/Catalog.php`)

This is the main product catalog page, implemented as a **full-page Livewire component**. It replaces the old static category/search pages with a reactive, filterable interface — no page reloads needed.

#### Public Properties (reactive state)
| Property | Default | Description |
|---|---|---|
| `$search` | `''` | Text search input (debounced 300ms) |
| `$selectedCategory` | `null` | Selected category ID |
| `$selectedFactions` | `[]` | Array of selected faction filters |
| `$selectedTypes` | `[]` | Array of selected apparel type filters |
| `$selectedColors` | `[]` | Array of selected paint color filters |
| `$selectedGenres` | `[]` | Array of selected game genre filters |
| `$selectedPlayers` | `[]` | Array of selected player count filters |
| `$sort` | `'newest'` | Sort order |
| `$categorySlug` | `null` | URL slug of the pre-selected category |

All properties except `$categorySlug` are synced to the **URL query string**, so filtered results are shareable/bookmarkable.

#### `mount($slug = null)`
Called once when the component initializes. If a `$slug` is passed (from the `/collections/{slug}` route), it looks up the matching Category and pre-sets `$selectedCategory`.

#### `updating($name, $value)`
Lifecycle hook. Fires before any property changes:
- If `selectedCategory` changes, resets all sub-filters (factions, types, colors, genres, players) to empty arrays — prevents stale cross-category filters.
- On any filter change, calls `$this->resetPage()` to jump back to page 1.

#### `render()`
Builds the product query dynamically based on current state:

1. **Name search** — `WHERE name LIKE '%search%'`
2. **Category filter** — `WHERE category_id = ?`
3. **Attribute filters** — Each filter (factions, types, colors, genres, players) uses `orWhereJsonContains('attributes->key', value)` — searches inside the JSON `attributes` column. Multiple selections use OR logic within a group.
4. **Sort** — `price_asc`, `price_desc`, or `newest` (default)
5. **Pagination** — 24 items per page

Also fetches **filter options** dynamically using raw SQL JSON extraction:
```php
Product::whereNotNull('attributes->faction')
    ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.faction')) as faction"))
    ->distinct()->pluck('faction')->filter();
```
This approach reads directly from MySQL's JSON column to get all distinct values currently in the database for each attribute.

Passes data to `livewire.shop.catalog` and uses `layouts.tenant` as its full-page layout.

---

## 9. Middleware

### `TenantAdmin` (`app/Http/Middleware/TenantAdmin.php`)

Applied to all protected admin routes. Checks two conditions:
1. The user is authenticated (`auth()->check()`)
2. The authenticated user has `is_admin = true`

If either fails, redirects to `admin.login` with an "Unauthorized access" error. This is the guard that prevents regular shop customers from reaching the admin panel even if they know the URL.

---

## 10. Blade Views & Layouts

### `layouts/tenant.blade.php`

The main storefront layout, used by all tenant-facing pages. Key sections:

**`<head>`**:
- Loads Cinzel (headings) and Outfit (body) from Bunny Fonts
- Injects Vite assets (`app.css`, `app.js`)
- Defines inline CSS for base body styles: dark background (`#0f172a`), glassmorphism card styles (`.wh-bg-card`), and gold border accent (`.wh-border`)

**Fixed background gradient**: A full-viewport `div` with a CSS gradient from indigo to yellow, creating the moody atmospheric base.

**Sticky header**:
- Brand logo ("Adept's Armoury") with a lightning bolt icon, glowing yellow
- Navigation links: Home, Departments (dropdown with 6 categories)
- Search form (hidden on mobile) pointing to `shop.catalog`
- Auth-aware user menu: shows username + dropdown (Profile, Administratum link if admin, Logout) when logged in; shows "Log In" button when guest
- Cart button with a live count badge (reads from session)

**Flash messages**: `session('success')` renders as a green banner; `session('error')` as a red banner, positioned just below the header.

**`<main>`**: A `$slot` container — this is where page content renders.

**Footer**: Minimal — the WH40K "In the grim darkness..." tagline, copyright.

---

### `components/product-card.blade.php`

An anonymous Blade component (no backing class) that accepts a `$product` prop. Used in the catalog grid, homepage carousels, and related products.

- **Image area**: If `image_url` is set, shows the image with a hover zoom effect (`scale-110`) and a gradient overlay from the bottom. If no image, shows a placeholder shield emoji.
- **Out of Stock badge**: Appears top-right if `$product->stock <= 0`.
- **Card body**:
  - Category name (yellow, uppercase)
  - Average rating display (star icon + number formatted to 1 decimal)
  - Product name (Cinzel font, links to product page)
  - Description (clamped to 2 lines)
  - Price in Euros (`price / 100` formatted with 2 decimals)
  - "Add to Cart" POST form button — disabled and greyed out if out of stock

Hover effect: subtle lift (`-translate-y-1`) + gold border glow.

---

## 11. Payment Flow (Stripe)

The payment flow follows this sequence:

```
Customer fills checkout form
        ↓
CheckoutController@store
  ├─ Validate request
  ├─ BEGIN DB Transaction
  │   ├─ firstOrCreate User by email
  │   ├─ Create Order (status: pending)
  │   └─ Create OrderItems
  ├─ Call Stripe API → create Checkout Session
  ├─ Save stripe_session_id on Order
  └─ COMMIT → redirect to Stripe hosted page

        ↓ (Stripe hosted payment page)

User pays on Stripe
        ↓
  ┌─────────────────────────────────────┐
  │ Two parallel paths:                 │
  │                                     │
  │  A) Stripe redirects to /success    │
  │     CheckoutController@success      │
  │     → find Order by stripe_session  │
  │     → set status: paid              │
  │     → clear cart session            │
  │                                     │
  │  B) Stripe POST to /stripe/webhook  │
  │     StripeWebhookController         │
  │     → verify signature              │
  │     → find Order by metadata/ID     │
  │     → set status: paid              │
  └─────────────────────────────────────┘
```

Path B (webhook) is the authoritative payment confirmation. Path A is the user-facing convenience redirect. Both check that the order is still `pending` before updating to avoid double-processing.

### Refunds
When an admin cancels a `paid`/`shipped`/`delivered` order:
1. The Stripe Checkout Session is retrieved to get the `payment_intent` ID.
2. `Stripe\Refund::create(['payment_intent' => $id])` issues a full refund.
3. Only if the refund succeeds does the order status change to `cancelled`.

---

## 12. Data Flow Diagrams

### Model Relationships

```
Country ─── Shop (Tenant)
              │
              └── [per-tenant database]
                    │
                    ├── Category
                    │     └── Product ──── ProductStock ─── PhysicalStore
                    │           │
                    │           └── Review
                    │                 └── User
                    │
                    ├── Order ─── OrderItem ─── Product
                    │     └── User
                    │
                    └── User
```

### Request Lifecycle (Tenant Request)

```
HTTP Request → domain lookup → InitializeTenancyByDomain
                                      ↓
                             Switch DB to tenant DB
                                      ↓
                     PreventAccessFromCentralDomains check
                                      ↓
                              Route matching
                                      ↓
                    [optional] TenantAdmin middleware
                                      ↓
                              Controller action
                                      ↓
                           Eloquent queries (tenant DB)
                                      ↓
                             Blade / Livewire view
                                      ↓
                              HTTP Response
```

---

*Documentation generated 2026-06-24. Covers all files in `app/`, `routes/`, `database/migrations/`, and core view files.*
