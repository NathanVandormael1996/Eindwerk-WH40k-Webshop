<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

use App\Http\Requests\Tenant\UpdateOrderStatusRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Refund;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $oldStatus = $order->status;
        $newStatus = $request->status;
        $refundSuccessMessage = '';

        // If transitioning to cancelled from paid/shipped/delivered and order has a Stripe session
        if ($newStatus === 'cancelled' && in_array($oldStatus, ['paid', 'shipped', 'delivered']) && $order->stripe_session_id) {
            try {
                Stripe::setApiKey(env('STRIPE_SECRET'));
                
                // Retrieve checkout session to get PaymentIntent ID
                $session = Session::retrieve($order->stripe_session_id);
                $paymentIntentId = $session->payment_intent;

                if ($paymentIntentId) {
                    Refund::create([
                        'payment_intent' => $paymentIntentId,
                    ]);
                    $refundSuccessMessage = ' Stripe refund of €' . number_format($order->total_amount / 100, 2) . ' issued successfully.';
                } else {
                    $refundSuccessMessage = ' Warning: No payment intent found to refund.';
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to cancel: Stripe refund failed. Details: ' . $e->getMessage());
            }
        }

        $order->update(['status' => $newStatus]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated.' . $refundSuccessMessage);
    }
}
