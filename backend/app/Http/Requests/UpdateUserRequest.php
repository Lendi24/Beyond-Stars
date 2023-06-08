<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
   
     public function rules()
     {
         $user = Auth::user();
     
         return [
             'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
             'first_name' => 'sometimes|string|max:255',
             'last_name' => 'sometimes|string|max:255',
             'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
             'password' => 'sometimes|string|min:8|confirmed',
         ];
     }
     
}
