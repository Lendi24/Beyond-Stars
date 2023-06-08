<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $input = $request->all();

        $user = User::create($input);

        // Dispatch the Registered event for the newly created user
        event(new Registered($user));

        $token = $user->createToken('DeviceAuth')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds',
            ], 401);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            // Send verification email
            $user->sendEmailVerificationNotification();

            return response([
                'message' => 'Email not verified. A verification link has been sent to your email.',
            ], 401);
        }

        $token = $user->createToken('DeviceAuth')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }




    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Tokens destroyed',
        ];
    }
}
