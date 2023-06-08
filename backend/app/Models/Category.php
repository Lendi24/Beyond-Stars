<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public function Event(){
        return $this->belongsToMany(Event::class);
    }

    protected $fillable = [
       'name'
    ];

    use HasFactory;
}
