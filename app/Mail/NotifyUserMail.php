<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $subject,
        public $name,
        public $text,
        public $systemName = null,
        public $fromAddress = null,
        public $fromName = null,
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