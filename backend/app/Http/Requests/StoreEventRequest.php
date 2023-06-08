<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        if ($this->group_id) {


            // Retrieve the group associated with the group_id passed in the request
            $group = Group::find($this->group_id);

            // If the group does not exist, the request is not authorized
            if (!$group) {
                return false;
            }

            // Get all UserGroupRoles for the authenticated user
            $userGroupRoles = $user->groupRoles;

            // Check if the user has a UserGroupRole for the group
            $userIsInGroup = $userGroupRoles->contains(function ($userGroupRole) use ($group) {
                return $userGroupRole->group_id === $group->id;
            });

            // The request is authorized if the user is in the group
            return $userIsInGroup;
        } elseif($user) {
            return true;
        }else{
            return false;
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
            'name' => ["required", "string"],
            'description' => ["required", "string"],
            'max_participants' => ["required", "integer"],
            'start_time' => ["required", function ($attribute, $value, $fail) {
                if (!($value instanceof Carbon)) {
                    $fail($attribute.' is not a valid Carbon instance.');
                }
            }],
            'end_time' => ["required", function ($attribute, $value, $fail) {
                if (!($value instanceof Carbon)) {
                    $fail($attribute.' is not a valid Carbon instance.');
                }
    
                // Verify that the start_time is less than end_time
                if ($this->start_time >= $value) {
                    $fail('The end time must be greater than the start time.');
                }
            }],
            'group_id' => ["required", "integer"],
            'location' => ["required", "string"],
            'category_id' => ['sometimes', 'integer'],
        ];
    }
    


protected function prepareForValidation()
{
       $data = [
        'group_id' => $this->groupId,
        'max_participants' => $this->maxParticipants,
        'start_time' => Carbon::parse($this->startTime),
        'end_time' => Carbon::parse($this->endTime),
    ];
    
    if ($this->has('categoryId')) {
        $data['category_id'] = $this->categoryId;
    }
    
    $this->merge($data);
}

    
}
