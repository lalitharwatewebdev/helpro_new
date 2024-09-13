<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // public function getCreatedAtAttribute($value){
    //     return \Carbon\Carbon::parse($value)->format("d/m/Y");
    // } 


}
