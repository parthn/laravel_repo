<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    protected $fillable = ['name','logo_url','from_email'];
    protected $hidden = ['company_id'];

    //


    public function roles()
    {
//       return $this->hasManyThrough(Role::class,FranchiseRole::class,'n1','n2','n3','n4');
        return $this->hasManyThrough(Role::class, FranchiseRole::class, 'franchise_id', 'name', 'id', 'role_name');
//        return $this->hasManyThrough(Franchise::class,UserFranchise::class,'user_id','id','id','franchise_id');
//        return $this->hasManyThrough(Franchise::class,UserFranchise::class,'user_id','franchise's primary key','user's primary key','user_franchises's franchise foreign key');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($franchise) {
            $franchise->database = encrypt($franchise->database);
            $franchise->db_user = encrypt($franchise->db_user);
            $franchise->db_password = encrypt($franchise->db_password);
        });
    }
}
