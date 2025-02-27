<?php

namespace App\Http\Controllers;

use App\Card;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DonationController extends Controller
{

    public function regular_donation(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'currency' => 'required',
            'amount' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'title' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'type' => 'required',
            'country' => 'required',
            'password' => 'required',
            'zip_code' => 'required',
            'city' => 'required',
            'address' => 'required',
            'card_number' => 'required',
            'expiry_date' => 'required',
            'cvv' => 'required',
        ]);
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'title' => $data['title'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'city' => $data['city'],
            'address' => $data['address'],
            'zip_code' => $data['zip_code'],
            'country' => $data['country'],
        ]);
        $user->donations()->create([
            'user_id' => $user->id,
            'currency' => $data['currency'],
            'amount' => $data['amount'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'type' => $data['type'],

        ]);
        Card::create([
            'user_id' => $user->id,
            'card_number' => $data['card_number'],
            'expiry' => $data['expiry_date'],
            'cvv' => $data['cvv'],
        ]);
        // dd($data);
        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Donation successfully registered');
    }
    // Friday donation
    public function friday_donation(Request $request)
    {
        // dd($request->all());
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'payment_method' => 'required',
            'message' => 'required'
        ]);
        return back()->with('success', 'Donation successful');
    }
    // Ramadan donation
    public function ramadan_donation(Request $request)
    {
        // dd($request->all());
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'payment_method' => 'required',
            'message' => 'required'
        ]);
        return back()->with('success', 'Donation successful');
    }
    public function show_donation()
    {
        $donations = Auth::user()->donations;
        return view('pages.donation.show', compact('donations'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
}
