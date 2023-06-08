<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    public function UserEventRole(){
        return $this->belongsToMany(UserEventRole::class);
    }

    public function UserGroupRole(){
        return $this->belongsToMany(UserGroupRole::class);
    }

    protected $fillable = [
        'title'
    ];

    use HasFactory;
}
