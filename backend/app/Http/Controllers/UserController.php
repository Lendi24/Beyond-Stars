<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\GroupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function groups()
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Retrieve all UserGroupRoles for the authenticated user
        $userGroupRoles = $user->groupRoles;

        // Map UserGroupRoles to their associated groups
        $groups = $userGroupRoles->map(function($userGroupRole) {
            return $userGroupRole->Group;
        });

        // Return the groups as a collection of GroupResources
        return GroupResource::collection($groups);
    }



    public function update(UpdateUserRequest $request) {
        $user = Auth::user();
        $input = $request->all();
    
        $user->update($input);
    
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    
    
    

}
