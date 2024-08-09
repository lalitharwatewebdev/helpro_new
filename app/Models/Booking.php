<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function labour()
    {
        return $this->belongsTo(User::class, "labour_id");
    }

    public function checkout()
    {
        return $this->belongsTo(Checkout::class,"checkout_id");
    }
}
