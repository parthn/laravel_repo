<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpartiePermission;

class Permission extends SpartiePermission
{
    protected $guard_name = 'web';
    protected $hidden = ['pivot', 'guard_name',
        'name'];

//    protected $fillable = ['name','guard_name','general_name','category_id'];

    //

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function setGuardNameAttribute()
    {
        \Log::info('isdsdsd');
        $this->attributes['guard_name'] = 'web';
    }

//    public function permissionRoles()
//    {
//        return $this->roles()->whereHas();
//    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permission) {
            $permission->guard_name = 'web';
        });

        static::updating(function ($permission) {
            $permission->guard_name = 'web';
        });
    }

}
