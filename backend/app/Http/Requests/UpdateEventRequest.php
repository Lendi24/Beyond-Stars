<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Models\UserEventRole;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $eventId = $this->route('event_id');

        // Retrieve the group information
        $event = Event::findOrFail($eventId);
        if(UserEventRole::where('user_id','=',$this->user()->id)->where('event_id','=',$event->id)->first())
        $userRole = UserEventRole::where('user_id','=',$this->user()->id)->where('event_id','=',$event->id)->first();
        else{
            return false;
        }
        // Check if the user making the request is the owner of the group
        if($userRole->role_id == 3 || $userRole->role_id == 2){
            return $this->user()->id === $event->owner_id;
        }

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
            return[
                'name' => ["sometimes","string"],
                'description' => ["sometimes","string"],
                'max_participants' => ["sometimes","integer"],
                'category_id' => ['sometimes', 'integer'],
                'start_time' =>["sometimes", function ($attribute, $value, $fail) {
                    if (!($value instanceof Carbon)) {
                        $fail($attribute.' is not a valid Carbon instance.');
                    }
                }],
                'end_time' => ["sometimes", function ($attribute, $value, $fail) {
                    if (!($value instanceof Carbon)) {
                        $fail($attribute.' is not a valid Carbon instance.');
                    }
        
                    // Verify that the start_time is less than end_time
                    if ($this->start_time >= $value) {
                        $fail('The end time must be greater than the start time.');
                    }
                }],
            ];
    }

    protected function prepareForValidation(){

        $data = [];

        if ($this->has('maxParticipants')) {
            $data['max_participants'] = $this->maxParticipants;
        }
    
        if ($this->has('startTime')) {
            $data['start_time'] = Carbon::parse($this->startTime);
        }

        if ($this->has('endTime')) {
            $data['end_time'] = Carbon::parse($this->endTime);
        }

        if ($this->has('categoryId')) {
            $data['category_id'] = intval($this->categoryId);
           
        }
    
        $this->merge($data);
    }

}
