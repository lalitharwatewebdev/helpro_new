<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeIndia($query){
        return $query->where("country_id","101");
    }

    public function user(){
        return $this->hasMany(User::class);
    }
}
