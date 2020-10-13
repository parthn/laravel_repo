<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FranchiseRole extends Model
{
    protected $hidden = ['deleted_at', 'created_at', 'updated_at', 'role_name', 'franchise_id'];

    //
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_name', 'name');
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }

    protected static function boot()
    {
        parent::boot();


        static::creating(function ($franchiseRole) {
            $role_name = Str::slug($franchiseRole->name, '_') . '_' . Str::orderedUuid();
            $franchiseRole->role_name = $role_name;
            $franchiseRole->franchise_id = app('franchise')->id;
//            $franchiseRole->guard_name = 'web';
        });

        static::created(function ($franchiseRole) {
            //create in Role Table
            $role_attributes = [];
            $role_attributes['name'] = $franchiseRole->role_name;
            Role::create($role_attributes);
        });
    }
}
