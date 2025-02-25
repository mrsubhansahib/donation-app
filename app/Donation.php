<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'currency', 'amount', 'start_date', 'end_date', 'type'
    ];
    public function user(){
        return $this->belongsTo(User::class);    
    }
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
    
}
