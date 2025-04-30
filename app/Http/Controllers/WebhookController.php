<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Invoice;
use App\Transaction;
use App\Subscription;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SubscriptionCreatedMail;
use App\Mail\SubscriptionCanceledMail;
use App\Mail\InvoicePaidMail;
use App\Mail\InvoiceFailedMail;
use App\Mail\TransactionFailedMail;
use App\Mail\TransactionPaidMail;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $object = $event->data->object;

        switch ($event->type) {
            case 'customer.subscription.created':
                $this->sendSubscriptionCreatedMail($object);
                break;

            case 'customer.subscription.deleted':
                $this->cancelSubscription($object);
                break;

            case 'invoice.paid':
                $this->createInvoiceAndTransaction($object);
                $this->sendInvoicePaidMail($object);
                break;

            case 'invoice.payment_failed':
                $this->createFailedInvoiceAndTransaction($object);
                $this->sendInvoiceFailedMail($object);
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function sendSubscriptionCreatedMail($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])->first();
        if ($subscription && $subscription->user) {
            Mail::to($subscription->user->email)->send(new SubscriptionCreatedMail($subscription->user, $subscription));
            Log::info("✅ Subscription created email sent to: " . $subscription->user->email);
        }
    }

    private function cancelSubscription($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])->first();
    
        if ($subscription) {
            // ✅ Update local DB to cancel
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now()
            ]);
    
            // ✅ Send email if user exists
            if ($subscription->user) {
                Mail::to($subscription->user->email)->send(new SubscriptionCanceledMail($subscription->user, $subscription));
                Log::info("✅ Subscription canceled email sent to: " . $subscription->user->email);
            }
    
            Log::info("🗂️ Local subscription marked as canceled: " . $subscription->id);
        } else {
            Log::warning("⚠️ Subscription not found for Stripe ID: " . $stripeSubscription['id']);
        }
    }
    

    private function createInvoiceAndTransaction($stripeInvoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice['subscription'])->first();

        if (!$subscription) return;

        // Create Invoice
        $invoice = Invoice::firstOrCreate(
            ['stripe_invoice_id' => $stripeInvoice['id']],
            [
                'subscription_id' => $subscription->id,
                'amount' => $stripeInvoice['amount_due'],
                'invoice_date' => now(),
                'status' => 'paid',
            ]
        );

        // Create Transaction
        $paymentId = $stripeInvoice['charge'] ?? $stripeInvoice['payment_intent'] ?? null;

        if ($paymentId && !Transaction::where('stripe_payment_id', $paymentId)->exists()) {
            Transaction::create([
                'invoice_id' => $invoice->id,
                'stripe_payment_id' => $paymentId,
                'status' => 'succeeded',
                'paid_at' => now(),
            ]);

            $user = $subscription->user;
            if ($user) {
                Mail::to($user->email)->send(new TransactionPaidMail($user, $invoice));
                Log::info("✅ Invoice + Transaction email sent to: " . $user->email);
            }
        }
    }


    private function createFailedInvoiceAndTransaction($stripeInvoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice['subscription'])->first();

        if (!$subscription) return;

        // Create failed invoice (if it doesn't exist)
        $invoice = Invoice::firstOrCreate(
            ['stripe_invoice_id' => $stripeInvoice['id']],
            [
                'subscription_id' => $subscription->id,
                'amount' => $stripeInvoice['amount_due'],
                'invoice_date' => now(),
                'status' => 'failed',
            ]
        );

        // Create failed transaction
        $paymentId = $stripeInvoice['charge'] ?? $stripeInvoice['payment_intent'] ?? null;

        if ($paymentId && !Transaction::where('stripe_payment_id', $paymentId)->exists()) {
            Transaction::create([
                'invoice_id' => $invoice->id,
                'stripe_payment_id' => $paymentId,
                'status' => 'failed',
                'paid_at' => null,
            ]);

            $user = $subscription->user;
            if ($user) {
                Mail::to($user->email)->send(new TransactionFailedMail($user, $invoice));
                Log::info("⚠️ Transaction failed email sent to: " . $user->email);
            }
        }
    }




    private function sendInvoicePaidMail($stripeInvoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice['subscription'])->first();
        if ($subscription && $subscription->user) {
            Mail::to($subscription->user->email)->send(new InvoicePaidMail($subscription->user, $stripeInvoice));
            Log::info("✅ Invoice paid email sent to: " . $subscription->user->email);
        }
    }


    private function sendInvoiceFailedMail($stripeInvoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice['subscription'])->first();
        if ($subscription && $subscription->user) {
            Mail::to($subscription->user->email)->send(new InvoiceFailedMail($subscription->user, $stripeInvoice));
            Log::info("⚠️ Invoice failed email sent to: " . $subscription->user->email);
        }
    }
}
