<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

            public function build()
        {
            return $this->subject('Reset Password Notification')
                        ->html('
                            <h1>Reset Password Request</h1>
                            <p>You are receiving this email because we received a password reset request for your account.</p>
                            <p>Token: {{ $this->token }}</p>
                            <p><a href="' . url('password/reset', $this->token) . '">Reset Password</a></p>
                            <p>If you did not request a password reset, no further action is required.</p>
                            <p>Thanks,<br>' . config('app.name') . '</p>
                        ');
        }

    
}
