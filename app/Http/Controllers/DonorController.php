<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index()
    {
        $user = auth()->user();


        $donors = User::where('role', 'donar')->orderBy('created_at', 'desc')->get();
        // dd($donors);
        return view('pages.admin.donors.index', compact('donors'));
    }

    public function show($id)
    {

        $user = User::find($id);
        $user->load(['subscriptions']);
        $subscriptions = $user->subscriptions()->orderBy('created_at', 'desc')->get();
        $transactions = $transactions = Transaction::whereHas('invoice.subscription', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('paid_at','desc')->get();
        $invoices = Invoice::whereHas('subscription', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('subscription')->orderBy('invoice_date','desc')->get();
        return view('pages.admin.donors.show', compact('user', 'transactions', 'invoices', 'subscriptions'));
    }
}
