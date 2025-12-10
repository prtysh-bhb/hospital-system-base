<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        $url = url('/reset-password/'.$this->token);

        return $this->subject('Reset Your Password - MediCare HMS')
            ->view('emails.reset_password', ['url' => $url]);
    }
}
