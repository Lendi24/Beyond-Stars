<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupCollection as ResourcesGroupCollection;
use App\Http\Resources\GroupResource;
use App\Http\Resources\UserCollection;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserEventRole;
use App\Models\UserGroupRole;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    //

    

    public function index(Request $request){

        function GetGroups($direction, $param){
    
            switch ($param) {
                case 'membCount':
                    return Group::select()->withCount('usergrouproles')->where('is_private','=','0')->where('invite_only','=','0')->orderBy('usergrouproles_count', $direction)->paginate();
                    break;
                case 'created':
                    return Group::select()->withCount('usergrouproles')->where('is_private','=','0')->where('invite_only','=','0')->orderBy('created_at', $direction)->paginate();
                    break;
                case 'updated':
                    return Group::select()->withCount('usergrouproles')->where('is_private','=','0')->where('invite_only','=','0')->orderBy('updated_at', $direction)->paginate();
                    break;
                default:
                    return Group::select()->withCount('usergrouproles')->where('is_private','=','0')->where('invite_only','=','0')->paginate(); 
                    break;
            }
        }
    
        $param = $request->input('param');
        $direction = $request->input('direction');
        $groups = GetGroups($direction,$param);    
    
        return new ResourcesGroupCollection($groups);
    }
    


    public function show($id)
{
    $group = Group::with('UserGroupRoles.User', 'Events')->where('id','=',$id)->first();
    return new GroupResource($group);
}



    public function create(StoreGroupRequest $request)
    {

        $owner_id = Auth::user()->id;

        $request->merge([
            'owner_id' => $owner_id
        ]);

        $group = Group::create(
           $request->all()
        );

        UserGroupRole::create([
            'user_id' => Auth::user()->id,
            'group_id' => $group->id,
            'role_id' => 3,
        ]);
        
        return response()->json([
            'status'=>'success',
            'message'=>'Group created successfully!'],201);
    }

    public function update(UpdateGroupRequest $request, $id){

        

        $group = Group::findOrFail($id);


        if ($request->has('name')) {
            $group->name = $request->input('name');
        }
        
        if ($request->has('owner_id')) {
            $group->owner_id = $request->input('owner_id');
        }
        
        if ($request->has('is_private')) {
            $group->is_private = $request->input('is_private');
        }

        if ($request->has('invite_only')) {
            $group->invite_only = $request->input('invite_only');
        }

        if($request->has('description')) {
            $group->description = $request->input('description');
        }
        
        $group->save();

        return response()->json([
            'message' => 'Group updated successfully.',
            'data' => $group,
        ], 200);


    }

    
    public function join($groupId)
    {

        if(UserGroupRole::where('user_id','=', Auth::user()->id)->where('group_id','=',$groupId)->first()){

            
        return response()->json([
            'status' => 'fail',
            'message' => 'Already joined this group'
        ],400);
            
        }


       
        UserGroupRole::create([
                
            'user_id' => Auth::user()->id,
            'group_id' => $groupId,
            'role_id' => 1,
                   
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Group joined successfully!'
        ],200) ;
        


    }

    public function leave($groupId){
        $group = Group::findOrFail($groupId);
        $groupRelation = UserGroupRole::where('group_id','=',$groupId)->where('user_id','=',Auth::user()->id)->first();
        
        if($groupRelation){
            // Check if the user is the group owner
            if($group->owner_id == Auth::user()->id) {
                // Find the next longest member (not including the current owner)
                $nextOwner = UserGroupRole::where('group_id','=',$groupId)
                                          ->where('user_id','<>',Auth::user()->id)
                                          ->orderBy('created_at', 'asc')
                                          ->first();
                
                // If there is no other member, delete the group
                if(!$nextOwner) {
                    $group->delete();
                    return response()->json(['status'=> 'success', 'message'=> 'Group deleted as there were no other members'],200);
                }
                
                // If there is another member, transfer the ownership
                $group->owner_id = $nextOwner->user_id;
                $group->save();
    
                // Change the role of the next owner to 'owner'
                $nextOwner->role_id = 3; // assuming 3 is the role_id for owner
                $nextOwner->save();
            }
            
            // Delete the user's group relation
            $groupRelation->delete();
        
            // Get all events that belong to this group
            $groupEvents = Event::where('group_id', $groupId)->get();
        
            // Delete UserEventRole relations for each event in the group
            foreach ($groupEvents as $event) {
                UserEventRole::where('user_id', Auth::user()->id)->where('event_id', $event->id)->delete();
            }
            
            return response()->json(['status'=> 'success', 'message'=> 'Successfully left the group and its events'],200);
        }
        else{
            return response()->json(['status'=> 'fail', 'message'=> 'User does not exist in this group'],400);
        }
    }
    
    
    

    public function showMembers($groupId){


        $userGroupRoles = UserGroupRole::select()->where('group_id','=',$groupId)->get();
        $users = [];
        foreach ($userGroupRoles as $user_id) {
           array_push($users,User::where('id','=',$user_id->user_id)->first());
        }
        
        return new UserCollection($users);
    }


    public function delete($groupId){
        $userId = Auth::user()->id;
        if($userId == Group::select('owner_id')->where('id','=',$groupId)->first()->owner_id){

           Group::where('id','=',$groupId)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Group has been deleted'
            ]);
        }


        return 'group not deleted';

    }
}
