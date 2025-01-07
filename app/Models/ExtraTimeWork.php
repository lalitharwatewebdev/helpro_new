<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraTimeWork extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function labour()
    {
        return $this->belongsToMany(User::class, "extra_time_work_labours", "extra_time_work_id", "labour_id");
    }

}
