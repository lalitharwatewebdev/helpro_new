<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

   

    public function user(){
        return $this->belongsTo(User::class,"user_id");
    }

    public function checkout(){
        return $this->belongsTo(Checkout::class,"checkout_id");
    }

   

}
