<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];
    

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function user(){
        return $this->belongsToMany(User::class,"category_user");
    }

    // labour booking relationship
    public function LabourBooking(){
        return $this->hasMany(LabourBooking::class);
    }

    
}
