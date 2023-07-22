<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $table = 'userpermissions';


    protected $fillable = [
        'user_id',
        'permission_id'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
