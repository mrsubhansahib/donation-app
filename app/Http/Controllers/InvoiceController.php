<?php

namespace App\Http\Controllers;

use App\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role == 'admin') {
            // Admin sees all invoices
            $invoices = Invoice::with('user')->latest()->get();
            return view('pages.admin.invoices.index', compact('invoices'));
        } else {
            // User sees only their invoices
            $invoices = Invoice::where('user_id', $user->id)->latest()->get();
            return view('pages.user.invoices.index', compact('invoices'));
        }
    }

    public function show($id)
    {
        $user = auth()->user();

        if ($user->role == 'admin') {
            $invoice = Invoice::all();
            return view('pages.admin.invoices.show', compact('invoices'));
        } else {
            // dd($invoice);
            $invoice = Auth::user()->invoices()->findOrFail($id);
            return view('pages.user.invoices.show', compact('invoice'));
        }
    }
}
