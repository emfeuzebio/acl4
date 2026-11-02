<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $resetUrl;
    public $systemName;
    public $fromAddress;
    public $fromName;

    public function __construct(
        $name,
        $resetUrl,
        $systemName = null,
        $fromAddress = null,
        $fromName = null,
    ) {
        $this->name        = $name;
        $this->resetUrl    = $resetUrl;
        $this->systemName  = $systemName;
        $this->fromAddress = $fromAddress;
        $this->fromName    = $fromName;
    }

    public function build()
    {
        $mail = $this
            ->subject('Recuperação de Senha')
            ->markdown('emails.forgot', [
                'systemName' => $this->systemName ?? config('app.name'),
            ]);

        if ($this->fromAddress) {
            $mail->from($this->fromAddress, $this->fromName ?: $this->systemName);
        }

        return $mail;
    }
}