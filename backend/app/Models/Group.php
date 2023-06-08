<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
   
    public function UserGroupRoles(){
        return $this->hasMany(UserGroupRole::class);
    }

    public function Events(){
        return $this->hasMany(Event::class);
    }

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'is_private',
        'invite_only'

    ];
}

