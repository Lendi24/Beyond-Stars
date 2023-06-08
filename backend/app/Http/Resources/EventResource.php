<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $group = $this->group_id ? Group::findOrFail($this->group_id) : null;
        $user = User::findOrFail($this->owner_id);

        $users = $this->userEventRoles->pluck('user');

        if($this->category_id){
            $category = Category::select('name', 'color','id')->where('id', $this->category_id)->first();
        }

        if($this->tags){
            $tags = $this->tags->pluck('name', 'id');
        }

        return [
            'id' => $this->id,
            'groupId' => $group ? $group->id : null ,
            'ownerId' => $user->id ,
            'name' => $this->name,
            'description' => $this->description,
            'maxParticipants' => $this->max_participants,
            'startTime' => $this->start_time,
            'endTime' => $this->end_time,
            'category' => $category ?? null,
            'tags' => $tags ?? null,
            'location' =>  $this->location,
            'users' => $users,
        ];
    }
}
