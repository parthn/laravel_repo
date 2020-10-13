<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $guard_name = 'web';

    protected $hidden = ['pivot', 'guard_name',
        'name'];
    protected $appends = [
//        'original_name'
    ];

    public function originalRole()
    {
        return $this->hasOne(FranchiseRole::class, 'role_name', 'name');
    }

    public function getAllWith()
        {
            $this->with('');
        }

    public function getOriginalNameAttribute()
    {
        $role = $this->originalRole()->first();
        if ($role) {
            return $role->name;
        }
        return '';
    }

    public function getCategoryWisePermissionsAttribute()
    {
        $categories = Category::with('permissions')->get();
        foreach ($categories as $category) {
            foreach ($category->permissions as $permission)
            $permission->has_permission = $this->hasPermissionTo($permission->id);
        }
        return $categories;

    }

    public function franchise()
    {
        return $this->hasOneThrough(Franchise::class, FranchiseRole::class, 'role_name', 'id', 'name', 'franchise_id');
    }
}
