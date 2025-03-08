<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use srtipe\invoice\invoice;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Subscription;
use Stripe\Charge;
use Stripe\Invoice;

class TransactionController extends Controller
{
    public function index()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntents = PaymentIntent::all();

        foreach ($paymentIntents->data as $paymentIntent) {
            $charges = Charge::all(['payment_intent' => $paymentIntent->id]);

            foreach ($charges->data as $charge) {
                $invoice = $charge->invoice ? Invoice::retrieve($charge->invoice) : null;

                $email = $invoice->customer_email ?? 'N/A';
                $user = User::where('email', $email)->first();
                // dd($user);
                // Check if transaction already exists
                if (!Transaction::where('stripe_payment_id', $charge->id)->exists()) {
                    Transaction::create([
                        'user_id'           => $user->id ?? 1,
                        'stripe_payment_id' => $charge->id,
                        'amount'            => $charge->amount / 100, // Convert to proper format
                        'currency'          => $charge->currency,
                        'status'            => $charge->status,
                        'paid_at'           => Carbon::createFromTimestamp($charge->created),
                        'type'              => 'payment', // You can modify this based on use case
                    ]);
                }
            }
        }
        // dd($transactions);

        $transactions = Transaction::with('user')->orderBy('paid_at', 'desc')->get();
        return view('pages.admin.transactions.index', compact('transactions'));
    }
    public function storeTransactionsFromStripe()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntents = PaymentIntent::all();

        foreach ($paymentIntents->data as $paymentIntent) {
            $charges = Charge::all(['payment_intent' => $paymentIntent->id]);

            foreach ($charges->data as $charge) {
                $invoice = $charge->invoice ? Invoice::retrieve($charge->invoice) : null;

                $email = $invoice->customer_email ?? 'N/A';
                $user = User::where('email', $email)->first();

                // Check if transaction already exists
                if (!Transaction::where('stripe_payment_id', $charge->id)->exists()) {
                    Transaction::create([
                        'user_id'           => $user->id ?? 1,
                        'stripe_payment_id' => $charge->id,
                        'amount'            => $charge->amount / 100, // Convert to proper format
                        'currency'          => $charge->currency,
                        'status'            => $charge->status,
                        'paid_at'           => Carbon::createFromTimestamp($charge->created),
                        'type'              => 'payment', // You can modify this based on use case
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Transactions saved successfully']);
    }
}
