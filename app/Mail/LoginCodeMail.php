<?php

namespace App\Mail;

use App\Models\User;
use App\Services\LoginCodes;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $code, public string $link) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "{$this->code} es tu código de acceso a MemoCar");
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.login-code',
            text: 'mail.login-code-text',
            with: ['minutes' => LoginCodes::TTL_MINUTES],
        );
    }
}
