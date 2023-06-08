<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function userEventRoles() {
        return $this->hasMany(UserEventRole::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class, 'event_tags');
    }

    public function group() {
        return $this->belongsTo(Group::class);
    }

    protected $fillable = [
        'name',
        'description',
        'max_participants',
        'start_time',
        'end_time',
        'group_id',
        'owner_id',
        'category_id',
        'location'
    ];
}
