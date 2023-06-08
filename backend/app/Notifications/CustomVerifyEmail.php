<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    protected function verificationUrl($notifiable)
    {
        // Your custom URL logic
        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify', // this should match the route name in web routes.
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Make sure you return the URL here.
        return str_replace(url('/api'), url('/#'), $temporarySignedUrl);
    }
}
