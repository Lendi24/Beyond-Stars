<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupRole extends Model
{

    public function Group(){
        return $this->belongsTo(Group::class);
    }

    public function User(){
        return $this->belongsTo(User::class);
    }

    public function Role(){
        return $this->hasOne(Role::class);
    }
    
    protected $fillable = [

        'group_id',
        'user_id',
        'role_id'
        

    ];

    use HasFactory;
}
