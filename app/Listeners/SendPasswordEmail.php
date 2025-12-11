<?php

namespace App\Listeners;

use App\Events\UserPasswordGenerated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPasswordEmail
{
    /**
     * Handle the event.
     */
    public function handle(UserPasswordGenerated $event): void
    {
        $user = $event->user;
        $password = $event->password;
        $userType = $event->userType;

        // Log the password generation
        Log::info('User Password Generated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => $userType,
            'name' => $user->full_name,
        ]);

        // TODO: Send actual email with password
        // Mail::to($user->email)->send(new WelcomePasswordMail($user, $password, $userType));

        Log::info('Password Email Placeholder', [
            'to' => $user->email,
            'subject' => "Welcome to MediCare HMS - Your Login Credentials",
            'user_type' => $userType,
            'message' => "Your temporary password: {$password}",
        ]);

        // TODO: Implement actual email sending
        /*
        Mail::send('emails.welcome_password', [
            'user' => $user,
            'password' => $password,
            'userType' => $userType,
            'loginUrl' => route('login'),
        ], function ($message) use ($user) {
            $message->to($user->email, $user->full_name)
                    ->subject('Welcome to MediCare HMS - Your Login Credentials');
        });
        */
    }
}
