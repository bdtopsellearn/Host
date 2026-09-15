<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                Log::info('Payment Intent succeeded', ['id' => $paymentIntent->id]);
                // We would typically update order status here if doing async checkout
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                Log::error('Payment Intent failed', ['id' => $paymentIntent->id]);
                break;
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                Log::info('Invoice payment succeeded', ['id' => $invoice->id]);
                break;
            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }
}
