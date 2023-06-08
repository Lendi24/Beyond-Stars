<?php

namespace App\Http\Requests;

use App\Models\Group;
use App\Models\UserGroupRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {


        $groupId = $this->route('group_id');

        // Retrieve the group information
        $group = Group::findOrFail($groupId);
        $userRole = UserGroupRole::where('user_id','=',$this->user()->id)->where('group_id','=',$group->id)->first();

        
        // Check if the user making the request is the owner of the group
        if($userRole->role_id == 3 || $userRole->role_id == 2){
            return true;
        }


    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [

            'name' => ['sometimes', 'string'],
            'description' => ['sometimes','string'],
            'owner_id' => ['sometimes','integer'],
            'is_private' => ['sometimes', 'boolean'],
            'invite_only' => ['sometimes','boolean'],

        ];
    }

    protected function prepareForValidation(){
        
        $data = [];
    
        if ($this->has('ownerId')) {
            $data['owner_id'] = intval($this->ownerId);
        }
    
        if ($this->has('isPrivate')) {
            $data['is_private'] =  boolval($this->isPrivate);
        }
    
        if($this->has('inviteOnly')){
            $data['invite_only'] = boolval($this->inviteOnly);
        }
    
        if($this->has('name')){
            $data['name'] = $this->name;
        }
    
        if($this->has('description')){
            $data['description'] = $this->description;
        }
        
    
        $this->merge($data);
    
    }
    

   protected function failedAuthorization()
{
    throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to perform this action.');
}
}
