<?php

namespace App\Http\Controllers;

use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Auth::user()->subscriptions;
        return view('pages.user.subscriptions.index', compact('subscriptions'));
    }

    public function show($id)
    {
        $subscription = Auth::user()->subscriptions()->findOrFail($id);
        return view('pages.user.subscriptions.show', compact('subscription'));
    }
}
