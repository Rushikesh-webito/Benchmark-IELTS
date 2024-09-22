<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordSetupEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $link)
    {
        $this->user = $user;
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject('Set Your Password')
                    ->view('emails.password_setup') // Create this view
                    ->with([
                        'user' => $this->user,
                        'link' => $this->link,
                    ]);
    }
}
