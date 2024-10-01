<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabourBooking extends Model
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

    public function category(){
        return $this->belongsTo(Category::class,"category_id");
    }

    public function address(){
        return $this->belongsTo(Address::class,"address_id");
    }

    public function LabourAcceptedBooking(){
        return $this->hasMany(LabourAcceptedBooking::class);
    }
}
