<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        //add the fields according to the users migration
        'first_name', 'last_name', 'title', 'email', 'password', 'city', 'address', 'zip_code', 'country', 'stripe_id', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //add this function to the User model
    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
    //add this function to the User model
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
    
    public function invoices(){
        return $this->hasMany(Invoice::class);
    }
    
    }
