<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    use HasFactory;
    protected $guarded = [];
        protected $table="otps";

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
        
    }
}
