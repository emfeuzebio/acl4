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
        protected ?string $replyToEmail = null,
        // 🔥 NOVO
        protected ?string $brandName = null,
        protected ?string $brandUrl = null,
        protected ?string $brandFooter = null,
    ) {}

    public function build(): self
    {
        // 🔥 Injeta as variáveis de branding no array de dados
        // O template usa {{ $brand_name }}, {{ $brand_url }}, {{ $brand_footer }}
        $dadosComBrand = array_merge($this->dados, [
            'brand_name'   => $this->brandName   ?? config('app.name'),
            'brand_url'    => $this->brandUrl    ?? config('app.url'),
            'brand_footer' => $this->brandFooter ?? config('app.name'),
        ]);

        $mailable = $this
            ->subject($this->assunto)
            ->markdown('emailservice::emails.' . $this->tpl, $dadosComBrand);

        if (!empty($this->fromAddress)) {
            $mailable->from(new Address($this->fromAddress, $this->fromName ?? ''));
        }

        if (!empty($this->replyToEmail)) {
            $mailable->replyTo($this->replyToEmail);
        }

        return $mailable;
    }
}