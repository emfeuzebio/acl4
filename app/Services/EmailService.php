<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotificationMail;
use App\Models\User;

class EmailService
{
    public function sendLoginNotification(User $user)
    {
        // Mail::to($user->email)->send(new LoginNotificationMail($user));
    }
}
