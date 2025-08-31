<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $name;
    public $text;

    public function __construct($subject,$name, $text)
    {
        $this->subject = $subject; 
        $this->name = $name;
        $this->text = $text;
    }

    public function build()
    {
        /**
         * Email usando view com Markdown FUNCIONANDO
         * No Laravel quando você usa Markdown views com: ->markdown('emails.nomeView')
         * O Laravel automaticamente torna públicas todas as propriedades públicas da classe 
         * Mailable e as disponibiliza na view.
         */
        return $this
            ->subject($this->subject)               // define o assunto do email
            ->markdown('emails.NotifyUserMail');    // especifica a view com o corpo do email
    }
}
