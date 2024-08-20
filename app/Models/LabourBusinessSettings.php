<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabourBusinessSettings extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table="labour_business_settings";

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
