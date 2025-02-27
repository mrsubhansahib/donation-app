<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role == 'admin') {
            // Admin sees all transactions
            $transactions = Transaction::with('donation', 'user')->latest()->get();
            return view('pages.admin.transactions.index', compact('transactions'));
        } else {
            // User sees only their transactions
            $transactions = Transaction::where('user_id', $user->id)->with('donation')->latest()->get();
            return view('pages.user.transactions.index', compact('transactions'));
        }
    }

    public function show($id)
    {
        $user = auth()->user();
        if ($user->role == 'admin') {
            $transaction = Transaction::all();
            return view('pages.admin.transactions.shpw', compact('transactions'));
        } else {
            $transaction = Auth::user()->transactions()->findOrFail($id);
            return view('pages.user.transactions.show', compact('transaction'));
        }
    }
}
