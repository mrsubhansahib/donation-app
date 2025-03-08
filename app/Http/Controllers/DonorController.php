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


        $donors = User::where('role', 'donar')->get();
        // dd($donors);
        return view('pages.admin.donors.index', compact('donors'));
    }

    public function show($id)
    {

        $user = User::find($id);
        $user->load(['subscriptions']);
        $transactions = $transactions = Transaction::whereHas('invoice.subscription', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->get();
        $invoices = Invoice::whereHas('subscription', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('subscription')->latest()->get();
        return view('pages.admin.donors.show', compact('user', 'transactions', 'invoices'));
    }
}
