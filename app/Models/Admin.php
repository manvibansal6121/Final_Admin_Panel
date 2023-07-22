<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use Notifiable,HasApiTokens;

    protected $guard = 'admin';

    // ...
     public function userpermissions()
    {
        return $this->belongsToMany(UserPermission::class);
    }
     public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'userpermissions', 'admin_id', 'permission_id');
    }

    public function type(): string
    {
        return "admin";
    }
    
}
