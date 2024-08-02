<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = [];
    


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'isAdmin',
        'firebase_uid',
        'device_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithoutAdmin($query)
    {
        return $query->where('isAdmin', '0');
    }

    // public function getNameAttribute()
    // {
    //     return $this->first_name . ' ' . $this->last_name;
    // }

    public function states()
    {
        return $this->belongsTo(State::class,"state");
    }

    public function cities()
    {
        return $this->belongsTo(City::class,"city");
    }

    public function labourImage(){
        return $this->hasMany(LabourImage::class,"user_id");
    }
}