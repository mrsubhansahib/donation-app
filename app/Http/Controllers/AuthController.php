<?php

namespace App\Http\Controllers;

use App\Donation;
use App\Invoice;
use App\Subscription;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if (Auth::attempt($data)) {
            return redirect()->route('dashboard')->with('success', 'Login successful');
        } else {
            return back()->with('error', 'Invalid login details');
        }
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    public function dashboard()
    {
        $total_donors = User::where('role', 'donar')->get()->count();
        $total_donations = Subscription::all()->count();
        // $total_transactions = Transaction::all()->count();
        $total_invoices = Invoice::all()->count();
        $user = auth()->user();
        $my_transactions = Transaction::whereHas('invoice.subscription', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->get();
        return view('dashboard', compact('total_donors', 'total_donations', 'total_invoices', 'my_transactions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function register(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'country' => 'required',
            'city' => 'required',
            'address' => 'required',
            'zip_code' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['stripe_id'] ='';
        $user = User::create($data);
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful');
    }
}
