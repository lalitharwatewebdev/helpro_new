<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $guard = 'admin';
    protected $fillable = [
        'name', 'email', 'password',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    // method to check if it is superadmin
    public function isSuperAdmin(){
        return $this->role == 'superadmin';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
