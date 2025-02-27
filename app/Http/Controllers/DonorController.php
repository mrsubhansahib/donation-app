<?php

namespace App\Http\Controllers;

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
        $user->load(['subscriptions', 'invoices', 'transactions']);
        return view('pages.admin.donors.show', compact('user'));
    }
}
