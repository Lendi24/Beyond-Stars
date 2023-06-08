<?php

namespace App\Http\Controllers;

use App\Mail\testmail;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;

class MailController extends Controller
{

    use Notifiable;

    public function allMail()
    {

        $users = User::select()->get();

        foreach ($users as $key => $value) {
            try {
                Mail::to($value->email)
                ->queue(new testmail());
            } catch (\Exception $e) {
                // Handle the exception here, e.g. log the error or do nothing
                continue; // continue to the next iteration of the loop
            }
        }

        
        return;
    }

  


}
