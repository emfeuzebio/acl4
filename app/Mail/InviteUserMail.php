<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $inviteUrl,
        public string $expiresAt,
        public string $systemName,
        public ?string $fromAddress = null,
        public ?string $fromName = null,
    ) {}

    public function build()
    {
        $mail = $this->subject('Convite para acessar ' . $this->systemName)
                     ->markdown('emails.InviteUserMail');

        if ($this->fromAddress) {
            $mail->from($this->fromAddress, $this->fromName ?: $this->systemName);
        }

        return $mail;
    }
}