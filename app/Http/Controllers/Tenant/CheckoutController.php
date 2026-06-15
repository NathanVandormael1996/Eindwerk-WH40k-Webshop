<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $total = 0;
        foreach ($products as $product) {
            $total += $product->price * $cart[$product->id];
        }

        return view('shop.checkout', compact('products', 'cart', 'total'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.home');
        }

        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
        ]);

        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalAmount = 0;
        
        $lineItems = [];

        foreach ($products as $product) {
            $quantity = $cart[$product->id];
            $totalAmount += $product->price * $quantity;
            
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $product->price,
                ],
                'quantity' => $quantity,
            ];
        }

        DB::beginTransaction();
        try {
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($products as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cart[$product->id],
                    'unit_price' => $product->price,
                ]);
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));
            
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('shop.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('shop.checkout.cancel'),
                'customer_email' => $request->email,
            ]);
            
            $order->update(['stripe_session_id' => $checkoutSession->id]);

            DB::commit();

            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to place order. ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('shop.home');
        }
        
        $order = Order::where('stripe_session_id', $sessionId)->first();
        if ($order && $order->status === 'pending') {
            $order->update(['status' => 'paid']);
            session()->forget('cart');
            return redirect()->route('shop.home')->with('success', 'Payment successful! Your order has been placed.');
        }

        return redirect()->route('shop.home');
    }

    public function cancel()
    {
        return redirect()->route('shop.cart')->with('error', 'Payment was cancelled.');
    }
}
