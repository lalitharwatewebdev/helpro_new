<?php

namespace App\Models;
use App\Models\Areas;
use App\Models\Category;
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
    
    public function area(){
        return $this->belongsTo(Areas::class,"area_id");
    }
    
    public function category(){
        return $this->belongsTo(Category::class,"category_id");
    }




}
