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

    public function __construct($name, $resetUrl)
    {
        $this->name = $name;
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        // com Markdown FUNCIONANDO
        return $this
            ->subject(config('app.name') . ' - Recuperação de Senha')
            ->markdown('emails.forgot');

        // com HTML    
        // return $this->view('emails.auth.forgot')
        //     ->with([
        //         'name' => $this->name,
        //         'url' => $this->resetUrl,
        //     ]);            
    }
}
