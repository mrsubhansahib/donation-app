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
                $this->createInvoice($object);
                $this->sendInvoicePaidMail($object);
                break;

            case 'invoice.payment_failed':
                $this->createFailedInvoice($object);
                $this->sendInvoiceFailedMail($object);
                break;

            case 'charge.succeeded':
                $this->createTransaction($object);
                break;

            case 'payment_intent.payment_failed':
                $this->createFailedTransaction($object);
                break;

                // case 'customer.subscription.updated':
                //     $this->updateSubscription($object);
                //     break;
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
            // $subscription->update([
            //     'status' => 'canceled',
            //     'canceled_at' => now(),
            // ]);

            if ($subscription->user) {
                Mail::to($subscription->user->email)->send(new SubscriptionCanceledMail($subscription->user, $subscription));
                Log::info("✅ Subscription canceled email sent to: " . $subscription->user->email);
            }
        }
    }

    private function createInvoice($stripeInvoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice['subscription'])->first();

        if ($subscription && !Invoice::where('stripe_invoice_id', $stripeInvoice['id'])->exists()) {
            Invoice::create([
                'subscription_id' => $subscription->id,
                'stripe_invoice_id' => $stripeInvoice['id'],
                'amount' => $stripeInvoice['amount_due'],
                'invoice_date' => now(),
                'status' => $stripeInvoice['status'],
            ]);
        }
    }

    private function createFailedInvoice($stripeInvoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice['subscription'])->first();

        if ($subscription && !Invoice::where('stripe_invoice_id', $stripeInvoice['id'])->exists()) {
            Invoice::create([
                'subscription_id' => $subscription->id,
                'stripe_invoice_id' => $stripeInvoice['id'],
                'amount' => $stripeInvoice['amount_due'],
                'invoice_date' => now(),
                'status' => 'failed',
            ]);
        }
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
            $user = $invoice->subscription->user ?? null;
            if ($user) {
                Mail::to($user->email)->send(new TransactionPaidMail($user, $invoice));
                Log::info("✅ Transaction succeeded email sent to: " . $user->email);
            }
        }
    }

    private function createFailedTransaction($paymentIntent)
    {
        $invoiceId = $paymentIntent['invoice'] ?? null;
        $invoice = Invoice::where('stripe_invoice_id', $invoiceId)->first();

        if ($invoice && !Transaction::where('stripe_payment_id', $paymentIntent['id'])->exists()) {
            Transaction::create([
                'invoice_id' => $invoice->id,
                'stripe_payment_id' => $paymentIntent['id'],
                'status' => 'failed',
                'paid_at' => null,
            ]);

            $user = $invoice->subscription->user ?? null;
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

    // private function updateSubscription($stripeSubscription)
    // {
    //     $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])->first();
    //     if ($subscription) {
    //         $subscription->update([
    //             'status' => $stripeSubscription['status'],
    //         ]);
    //     }
    // }
}
