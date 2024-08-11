<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function states()
    {
        return $this->belongsTo(State::class, "state_id");
    }

    public function cities()
    {
        return $this->belongsTo(City::class, "city_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
