<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Group;
use App\Models\Tag;
use App\Models\UserEventRole;
use App\Models\UserGroupRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    // public function index($year) old function ree
    // {
    //     $eventRole = UserEventRole::select()->where('user_id','=',Auth::user()->id)->get();        
    //     $events = [];

    //     foreach ($eventRole as $key => $value) {
    //         if(substr($value->event->start_time,0,4)==$year||substr($value->event->end_time,0,4)==$year||(substr($value->event->start_time,0,4)<$year&&substr($value->event->end_time,0,4)>$year)){
    //             $events = Arr::add($events, intval($key), $value->event);
    //         }
    //     }

    //     return new EventCollection($events);
    // }

    // public function index($startDate,$endDate) {
    //     $events = UserEventRole::select()->where('user_id','=',Auth::user()->id)->get();        
    //     //** TODO: We need to get events within a range by supporting startDate and endDate parameters.  */
    //     //** TODO: Support If-Modified-Since header argument  */
    //     $eventRole = UserEventRole::select()->where('user_id','=',Auth::user()->id)->get();        
    //     $events = [];

    //     foreach ($eventRole as $key => $value) {
    //         if(substr($value->event->start_time,0,4)==2023||substr($value->event->end_time,0,4)==2023||(substr($value->event->start_time,0,4)<2023&&substr($value->event->end_time,0,4)>2023)){
    //             $events = Arr::add($events, intval($key), $value->event);
    //         }
    //     }


    //     return new EventCollection($events);
    // }

    public function index(Request $request)
    {

        // Convert string dates to Carbon instances
        $startDate = Carbon::parse($request->query('startDate'));
        $endDate = Carbon::parse($request->query('endDate'));

        $events = UserEventRole::where('user_id', Auth::user()->id)
            ->whereHas('event', function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_time', [$startDate, $endDate]);
                    $q->orWhereBetween('end_time', [$startDate, $endDate]);
                })
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_time', '<=', $startDate)
                            ->where('end_time', '>=', $endDate);
                    });
            })
            ->with('event') // eager load the events
            ->get()
            ->pluck('event'); // collect only the event instances

        // if ($request->header('If-Modified-Since')) {

        //     $lastModified = Carbon::parse($request->header('If-Modified-Since'));
        //     $events = $events->filter(function ($event) use ($lastModified) {

        //         return $event->updated_at > $lastModified;
        //     });
        // }

        return new EventCollection($events);
    }

    public function publicIndex(){
        $events = Event::whereHas('group', function ($query) {
            $query->where('is_private', '=', false);
        })->paginate(); 
    
        return new EventCollection($events);
    }
    

    public function groupIndex(Request $request)
    {
        return new EventCollection(Event::where('group_id', '=', $request->input('group_id'))->get());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(StoreEventRequest $request)
{
    $request->merge([
        'owner_id' => Auth::user()->id
    ]);

    $event = Event::create($request->all());

    UserEventRole::create([
        'user_id' => Auth::user()->id,
        'event_id' => $event->id,
        'role_id' => 3 // assuming 3 is the role_id for owner
    ]);
    
    // if tags are included in the request
    if ($request->has('tags')) {
        $tags = json_decode($request->tags);
        foreach($tags as $tagName){
            // Check if tag already exists in the database
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            
            // Attach the tag to the event
            $event->tags()->attach($tag->id);
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Event created successfully!'
    ], 201);
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $event = Event::with('userEventRoles.user')->where('id', $id)->get()->first();

        return new EventResource($event);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventRequest $request, $id)
    {
        $event = Event::findOrFail($id);


        if ($request->has('name')) {
            $event->name = $request->input('name');
        }
        if ($request->has('owner_id')) {
            $event->owner_id = $request->input('owner_id');
        }
        if ($request->has('description')) {
            $event->description = $request->input('description');
        }
        if ($request->has('max_participants')) {
            $event->max_participants = $request->input('max_participants');
        }
        if ($request->has('start_time')) {
            $event->start_time = $request->input('start_time');
        }
        if ($request->has('end_time')) {
            $event->end_time = $request->input('end_time');
        }
        if ($request->has('category_id')) {
            $event->category_id = $request->input('category_id');
        }


        $event->save();


         if ($request->has('tags')) {
        $tags = json_decode($request->tags, true);
        
        // Detach all existing tags first
        $event->tags()->detach();

        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $event->tags()->attach($tag->id);
        }

    }


        return response()->json([
            'message' => 'Event updated successfully.',
            'data' => $event,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Event::destroy($id);
    }

    public function join($id)
{
    $event = Event::findOrFail($id);

    if($event->max_participants == $event->userEventRoles->count()){
        return response()->json([
            'message' => 'Event is full.',
        ], 400);
    }
    // Check if the user belongs to the group associated with the event
    $groupMember = UserGroupRole::where('user_id', Auth::id())
        ->where('group_id', $event->group_id)
        ->first();

    if (!$groupMember) {
        return response()->json([
            'message' => 'You are not a member of the group associated with this event.',
        ], 400);
    }

    // Check if the user has already joined the event
    $existingRole = UserEventRole::where('user_id', Auth::id())
        ->where('event_id', $event->id)
        ->first();

    if ($existingRole) {
        return response()->json([
            'message' => 'You have already joined the event.',
        ], 400);
    }

    // Create a new UserEventRole for the authenticated user and the event
    $userEventRole = new UserEventRole([
        'user_id' => Auth::user()->id,
        'event_id' => $event->id,
        'role_id' => 1,
    ]);
    $userEventRole->save();

    return response()->json([
        'message' => 'You have joined the event.',
    ], 200);
}


public function leave($eventId)
{
    $event = Event::findOrFail($eventId);
    $userEventRole = UserEventRole::where('user_id', Auth::user()->id)
                                  ->where('event_id', $eventId)
                                  ->first();

    if($userEventRole) {
        // Check if the user is the event owner
        if($event->owner_id == Auth::user()->id) {
            // Find the next longest participant (not including the current owner)
            $nextOwner = UserEventRole::where('event_id', $eventId)
                                      ->where('user_id','<>', Auth::user()->id)
                                      ->orderBy('created_at', 'asc')
                                      ->first();

            // If there is no other participant, delete the event
            if(!$nextOwner) {
                $event->delete();
                return response()->json(['status'=> 'success', 'message'=> 'Event deleted as there were no other participants'],200);
            }

            // If there is another participant, transfer the ownership
            $event->owner_id = $nextOwner->user_id;
            $event->save();

            // Change the role of the next owner to 'owner'
            $nextOwner->role_id = 3; // assuming 3 is the role_id for owner
            $nextOwner->save();
        }

        // Delete the user's event relation
        $userEventRole->delete();

        return response()->json(['status'=> 'success', 'message'=> 'Successfully left the event'],200);
    } 
    else {
        return response()->json(['status'=> 'fail', 'message'=> 'User not part of this event'],400);
    }
}

}
