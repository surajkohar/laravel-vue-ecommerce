<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function build()
    {
        return $this->subject('Welcome to ' . config('app.name'))
                    ->view('emails.welcome')
                    ->with([
                        'name' => $this->mailData['name'],
                        'email' => $this->mailData['email'],
                        'password' => $this->mailData['password'],
                        'customMessage' => $this->mailData['customMessage'],
                        'loginUrl' => $this->mailData['loginUrl'],
                    ]);
    }
}
