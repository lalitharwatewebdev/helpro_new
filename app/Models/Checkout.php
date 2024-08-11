<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, "address_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }




}
