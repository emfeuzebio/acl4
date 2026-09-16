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
        protected string  $tpl,
        protected string  $assunto,
        protected array   $dados = [],
        protected ?string $fromAddress = null,
        protected ?string $fromName = null,
        protected ?string $replyToEmail = null,   // ← renomeado
    ) {}

    public function build(): self
    {
        $mailable = $this
            ->subject($this->assunto)
            ->markdown('emailservice::emails.' . $this->tpl, $this->dados);

        if (!empty($this->fromAddress)) {
            $mailable->from(new Address($this->fromAddress, $this->fromName ?? ''));
        }

        if (!empty($this->replyToEmail)) {
            $mailable->replyTo($this->replyToEmail);
        }

        return $mailable;
    }
}