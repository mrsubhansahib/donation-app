<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Auth::user()->transactions;
        return view('pages.user.transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Auth::user()->transactions()->findOrFail($id);
        return view('pages.user.transactions.show', compact('transaction'));
    }
}
