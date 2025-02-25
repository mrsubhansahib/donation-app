<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'donation_id', 'status', 'attempt_date'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function donation(){
        return $this->belongsTo(Donation::class);
    }

}
