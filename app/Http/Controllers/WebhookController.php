<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\Transaction;
use App\Subscription;
use App\Models\User; // 👈 Make sure to use User model
use App\Mail\SubscriptionCreatedMail; // 👈 Add Mailable class
use Stripe\Stripe;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // 👈 Mail facade
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        // Logging for debugging
        Log::info('📩 Stripe Webhook Headers:', ['headers' => $request->headers->all()]);
        Log::info('📩 Stripe Webhook Payload:', ['payload' => json_decode($payload, true)]);

        // For testing purpose only — add your event logic here
        $event = json_decode($payload, true);

        if ($event['type'] === 'customer.subscription.created') {
            $this->sendSubscriptionCreatedMail($event['data']['object']); // 👈 Mail function call
        }

        return response()->json(['status' => 'received']);
    }

    // 📬 Function to send subscription created email
    private function sendSubscriptionCreatedMail($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])->first();

        if ($subscription) {
            $user = $subscription->user; // assuming `user()` relationship exists in Subscription model

            if ($user) {
                Mail::to($user->email)->send(new SubscriptionCreatedMail($user, $subscription));
                Log::info("✅ Subscription created email sent to: " . $user->email);
            } else {
                Log::warning("⚠️ User not found for subscription ID: " . $subscription->id);
            }
        } else {
            Log::warning("⚠️ Subscription not found for Stripe ID: " . $stripeSubscription['id']);
        }
    }

    // Existing functions...

    private function createInvoice($stripeInvoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice['subscription'])->first();

        if ($subscription && !Invoice::where('stripe_invoice_id', $stripeInvoice['id'])->exists()) {
            Invoice::create([
                'subscription_id' => $subscription->id,
                'stripe_invoice_id' => $stripeInvoice['id'],
                'amount' => $stripeInvoice['amount_due'] / 100,
                'invoice_date' => now(),
                'status' => $stripeInvoice['status'],
            ]);
        }
    }

    private function updateInvoiceStatus($stripeInvoice)
    {
        Invoice::where('stripe_invoice_id', $stripeInvoice['id'])->update([
            'status' => 'paid',
        ]);
    }

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

    private function updateSubscription($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])->first();

        if ($subscription) {
            $subscription->update([
                'status' => $stripeSubscription['status'],
            ]);
        }
    }

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
