<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    //

    public function verify(Request $request)
    {
        $user = \App\Models\User::find($request->route('id'));
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified!'
            ]);
        }
        $user->markEmailAsVerified();
        return response()->json([
            'message' => 'Email verified!'
        ]);
    }
}
