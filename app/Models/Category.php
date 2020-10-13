<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $hidden = ['deleted_at'];

    protected $fillable = ['name'];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'category_id');
    }
}
