<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptedBooking extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function labour(){
        return $this->belongsTo(User::class,"labour_id");
    }

    public function booking(){
        return $this->belongsTo(Booking::class,"booking_id");
    }
}
