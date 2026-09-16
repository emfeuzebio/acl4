<?php

namespace App\EmailService\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class GenericMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string  $template,
        public string  $assunto,
        public array   $dados = [],
        public ?string $fromAddress = null,
        public ?string $fromName = null,
        public ?string $replyTo = null,
    ) {}

    public function build(): self
    {
        $mailable = $this
            ->subject($this->assunto)
            ->markdown('emailservice::emails.' . $this->template, $this->dados);

        if (!empty($this->fromAddress)) {
            $mailable->from(new Address($this->fromAddress, $this->fromName ?? ''));
        }

        if (!empty($this->replyTo)) {
            $mailable->replyTo($this->replyTo);
        }

        return $mailable;
    }
}