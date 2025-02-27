<?php

namespace App\Http\Controllers;

use App\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Auth::user()->invoices;
        return view('pages.user.invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = Auth::user()->invoices()->findOrFail($id);
        // dd($invoice);
        return view('pages.user.invoices.show', compact('invoice'));
    }
}
