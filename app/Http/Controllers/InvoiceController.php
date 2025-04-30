<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;

class InvoiceController extends Controller
{
    public function index()
    {
        

        // Retrieve invoices based on user role
        $user = auth()->user();

        if ($user->role == 'admin') {
            // Admin sees all invoices
            $invoices = Invoice::with('subscription')->orderBy('invoice_date', 'desc')->get();
            return view('pages.admin.invoices.index', compact('invoices'));
        } else {
            // User sees only their invoices
            $invoices = Invoice::whereHas('subscription', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('subscription')->orderBy('invoice_date', 'desc')->get();
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
