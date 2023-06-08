<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    public function RolePermission(){
        return $this->belongsToMany(RolePermission::class);
    }

    protected $fillable = [
        'name'
    ];

    use HasFactory;
}
