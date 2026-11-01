<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subject,
        public string $name,
        public string $text,
        public ?string $systemName = null,
        public ?string $fromAddress = null,
        public ?string $fromName = null,
    ) {}

    public function build()
    {
        $mail = $this
            ->subject($this->subject)
            ->markdown('emails.NotifyUserMail', [
                'systemName' => $this->systemName ?? config('app.name'),
            ]);

        if ($this->fromAddress) {
            $mail->from($this->fromAddress, $this->fromName ?: $this->systemName);
        }

        return $mail;
    }
}