<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function franchises()
    {
        return $this->hasManyThrough(Franchise::class, UserFranchise::class, 'user_id', 'id', 'id', 'franchise_id');
//        return $this->hasManyThrough(Franchise::class,UserFranchise::class,'user_id','franchise's primary key','user's primary key','user_franchises's franchise foreign key');
    }


    public function userFranchises()
    {
        return $this->hasMany(UserFranchise::class, 'user_id');
    }


    public function singleRoleOfFranchise()
    {
        $role = $this->roles()
            ->whereHas('franchise', function ($q) {
//                $q->where('franchise.id', app('franchise')->id);
                $q->where('franchises.id', request('franchise_id'));
            })
            ->first();
        return $role;
    }

    public function myHasPermissionTo($permission)
    {
        $roles = $this->roles()
            ->whereHas('franchise', function ($q) {
//                $q->where('franchise.id', app('franchise')->id);
                $q->where('franchises.id', request('franchise_id'));
            })
            ->get();
        foreach ($roles as $role) {
            if ($role->hasPermissionTo($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAccessToFranchise($franchise_id)
    {
        $userFranchises = $this->userFranchises()->where('franchise_id', $franchise_id)->first();
        return $userFranchises ? true : false;
//        return $this->hasManyThrough(Franchise::class,UserFranchise::class,'user_id','franchise's primary key','user's primary key','user_franchises's franchise foreign key');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->remember_token = Str::random(60);
        });
    }


}
