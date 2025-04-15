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
        // com Markdown
        return $this
            ->subject('Recuperação de Senha')
            ->markdown('emails.forgot');

        // com HTML    
        // return $this->view('emails.auth.forgot')
        //     ->with([
        //         'name' => $this->name,
        //         'url' => $this->resetUrl,
        //     ]);            
    }
}

/*

[2025-04-15 14:27:20] production.ERROR: 
View [emails.auth.forgot] not found. {"exception":"[object] (InvalidArgumentException(code: 0): 
    View [emails.auth.forgot] not found. at 
    /home/u999320335/domains/fazcomphp.com.br/public_html/acl4/vendor/laravel/framework/src/Illuminate/View/FileViewFinder.php:139)

*/