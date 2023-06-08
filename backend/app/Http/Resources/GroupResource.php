<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'description' => $this->description,
        'owner_id' => $this->owner_id,
        'is_private' => $this->is_private,
        'invite_only' => $this->invite_only,
        'users' => UserResource::collection($this->whenLoaded('UserGroupRoles.User')),
        'events' => EventResource::collection($this->whenLoaded('Events'))
    ];
}



        
    }
