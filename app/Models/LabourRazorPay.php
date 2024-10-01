<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabourRazorPay extends Model
{
    use HasFactory;
    protected $table="labour_razor_pay";
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
