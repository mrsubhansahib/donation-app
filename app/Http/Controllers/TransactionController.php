<?php

namespace App\Http\Controllers;

use App\Invoice as AppInvoice;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
// use srtipe\invoice\invoice;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Stripe\Invoice;

class TransactionController extends Controller
{
    public function index()
    {
        
        $user = auth()->user();

        if ($user->role == 'admin') {
            // $transactions = Transaction::all();
            $transactions = Transaction::with('invoice.subscription.user')->orderBy('paid_at', 'desc')->get();
            return view('pages.admin.transactions.index', compact('transactions'));
        } else {
            // User sees only their invoices
            $transactions = Transaction::whereHas('invoice.subscription', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('paid_at', 'desc')->get();
            return view('pages.user.transactions.index', compact('transactions'));
        }
    }
}
