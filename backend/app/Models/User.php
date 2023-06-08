<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;



    public function groups()
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    public function groupRoles()
    {
        return $this->hasMany(UserGroupRole::class, 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'owner_id');
    }

    public function eventRoles()
    {
        return $this->hasMany(UserEventRole::class, 'user_id');
    }

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public static function search($request)
    {
        $users = User::query();

        if (isset($request['search'])) {
            $users = $users->where('username', 'like', '%' . $request['search'] . '%');
        }

        return $users->paginate(15);
    }

    public function setPassword($password)
    {
        $this->update(['password' => $password]);
    }
}
