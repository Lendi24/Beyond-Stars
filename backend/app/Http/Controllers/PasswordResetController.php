<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class PasswordResetController extends Controller
{
    
    public function forgotPassword(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    if ($user) {
        $newPassword = Str::random(10); // Generate a random 10 character password
        $user->password = $newPassword;
        $user->save();

        try {
            Mail::to($request->email)->send(new ResetPasswordMail($newPassword));
            return response()->json(['message' => 'Password reset email sent']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send password reset email', 'error' => $e->getMessage()], 500);
        }
    }

    return response()->json(['message' => 'No account found with this email address'], 404);
}

    



}
