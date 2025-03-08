<?php

namespace App\Http\Controllers;

use App\Invoice as AppInvoice;
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

        $paymentIntents = PaymentIntent::all(['limit' => 10]);

        foreach ($paymentIntents->data as $paymentIntent) {
            $charges = Charge::all(['payment_intent' => $paymentIntent->id]);

            foreach ($charges->data as $charge) {
                $invoice = $charge->invoice ? Invoice::retrieve($charge->invoice) : null;

                $email = $invoice->customer_email ?? 'N/A';
                $user = User::where('email', $email)->first();
                $invoice = AppInvoice::where('stripe_invoice_id', $invoice->id)->first();
                // dd($invoice);
                // Check if transaction already exists
                if ($invoice && !Transaction::where('stripe_payment_id', $charge->id)->exists()) {
                    Transaction::create([
                        'invoice_id' => $invoice->id,
                        'stripe_payment_id' => $charge->id,
                        'status'            => $charge->status,
                        'paid_at'           => Carbon::createFromTimestamp($charge->created),
                    ]);
                }
            }
        }

        $user = auth()->user();

        if ($user->role == 'admin') {
            $transactions = Transaction::all();
            return view('pages.admin.transactions.index', compact('transactions'));
        } else {
            // User sees only their invoices
            $transactions = Transaction::whereHas('invoice.subscription', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->latest()->get();
            return view('pages.user.transactions.index', compact('transactions'));
        }
    }
}
