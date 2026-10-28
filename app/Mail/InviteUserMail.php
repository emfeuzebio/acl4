<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $inviteUrl;
    public string $expiresAt;

    public function __construct(string $name, string $inviteUrl, string $expiresAt)
    {
        $this->name      = $name;
        $this->inviteUrl = $inviteUrl;
        $this->expiresAt = $expiresAt;
    }

    public function build()
    {
        return $this
            ->subject('Convite para acessar ' . config('app.name'))
            ->markdown('emails.InviteUserMail');
    }
}