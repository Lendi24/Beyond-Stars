<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{

    public function Role(){
        return $this->belongsTo(Role::class);
    }

    protected $fillable = [
        'name'
    ];

    use HasFactory;
}
