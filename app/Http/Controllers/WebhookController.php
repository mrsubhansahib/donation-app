<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\Transaction;
use App\Subscription;
use Stripe\Stripe;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
    
        Log::info('📩 Stripe Webhook Headers:', ['headers' => $request->headers->all()]);
        Log::info('📩 Stripe Webhook Payload:', ['payload' => json_decode($payload, true)]);
    
        return response()->json(['status' => 'received']);
    }

    // Function to create invoice in DB
    private function createInvoice($stripeInvoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice['subscription'])->first();

        if ($subscription && !Invoice::where('stripe_invoice_id', $stripeInvoice['id'])->exists()) {
            Invoice::create([
                'subscription_id' => $subscription->id,
                'stripe_invoice_id' => $stripeInvoice['id'],
                'amount' => $stripeInvoice['amount_due'] / 100, // Convert from cents
                'invoice_date' => now(),
                'status' => $stripeInvoice['status'],
            ]);
        }
    }

    // Function to update invoice status
    private function updateInvoiceStatus($stripeInvoice)
    {
        Invoice::where('stripe_invoice_id', $stripeInvoice['id'])->update([
            'status' => 'paid',
        ]);
    }

    // Function to create transactions
    private function createTransaction($stripeCharge)
    {
        $invoice = Invoice::where('stripe_invoice_id', $stripeCharge['invoice'])->first();

        if ($invoice && !Transaction::where('stripe_payment_id', $stripeCharge['id'])->exists()) {
            Transaction::create([
                'invoice_id' => $invoice->id,
                'stripe_payment_id' => $stripeCharge['id'],
                'status' => 'succeeded',
                'paid_at' => now(),
            ]);
        }
    }

    // Function to update subscription status
    private function updateSubscription($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])->first();

        if ($subscription) {
            $subscription->update([
                'status' => $stripeSubscription['status'],
            ]);
        }
    }

    // Function to handle canceled subscriptions
    private function cancelSubscription($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }
    }
}
