# Adept's Armoury — Installation & Setup Guide

This guide covers the local development setup for the Adept's Armoury multi-tenant e-commerce platform.

## 1. Prerequisites
Ensure your local machine has the following installed:
* **PHP:** ^8.3
* **Composer:** ^2.0
* **Node.js & NPM:** (Latest LTS recommended)
* **Database:** MySQL 8.0+ or MariaDB
* **Stripe CLI:** Required for local webhook testing. *(Tip for Arch/CachyOS users: you can grab this directly from the AUR via `yay -S stripe-cli`)*.

## 2. Environment Setup
Clone the repository and set up your environment variables.

```bash
# Copy the example env file
cp .env.example .env

# Generate the application key
php artisan key:generate
```

Open your `.env` file and configure your database connection. Because this is a multi-tenant application, this connection acts as your **Central Database**. The tenant databases will be created automatically.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adepts_central_db  # Create this empty database in MySQL first!
DB_USERNAME=root
DB_PASSWORD=
```

Also, add your Stripe test keys to the `.env`:
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_... # You will get this from the Stripe CLI later
```

## 3. Install Dependencies
Pull in all backend and frontend packages.

```bash
composer install
npm install
```

## 4. Database Setup & Tenancy
Run the migrations for the central database and seed it with your initial data. This will build your central tables (`tenants`, `domains`, `admins`) and the seeder will automatically generate your first shop.

```bash
php artisan migrate --seed
```
*Note: When the seeder creates the initial shop in the central database, Stancl/Tenancy will automatically create a new database for it (e.g., `tenantshop1`) and run the tenant migrations inside it.*

To populate your newly created shop with its initial categories, products, and specific tenant data, run the tenant seeders:

```bash
php artisan tenants:seed
```

## 5. Local Development Server
To run the application locally, you need to configure your local domain routing. If you are using Laravel Valet, this is handled automatically. If you are using the built-in PHP server, you need to specify the host.

Start the frontend build process in your first terminal:
```bash
npm run dev
```

Start the backend server in your second terminal:
```bash
php artisan serve --host=shop1.localhost --port=8000
```
You can now access your shop at: `http://shop1.localhost:8000`

## 6. Stripe Webhook Testing
To process payments and test the auto-refund functionality locally, you must forward Stripe events to your local server.

Open a third terminal and run the Stripe CLI:
```bash
stripe listen --forward-to http://shop1.localhost:8000/stripe/webhook
```
The CLI will output a webhook signing secret (`whsec_...`). Copy this secret and paste it into your `.env` file as `STRIPE_WEBHOOK_SECRET`. 

Restart your `php artisan serve` terminal to load the new environment variable. Your local setup is now fully operational!
