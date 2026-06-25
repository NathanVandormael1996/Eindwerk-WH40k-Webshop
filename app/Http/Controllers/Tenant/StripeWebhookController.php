<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            
            // Find order by metadata.order_id or stripe_session_id
            $orderId = $session->metadata->order_id ?? null;
            $order = null;
            
            if ($orderId) {
                $order = Order::find($orderId);
            }
            
            if (!$order) {
                $order = Order::where('stripe_session_id', $session->id)->first();
            }

            if ($order && $order->status === 'pending') {
                $order->update(['status' => 'paid']);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
