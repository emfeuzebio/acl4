<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class EmailTesteController extends Controller
{
    public function enviar()
    {
        // Destinatário do teste
        $destinatario = 'emfeuzebio72@gmail.com'; // ← Troque pelo seu email pessoal

        // Enviar usando o mailer 'legado'
        Mail::mailer('legado')
            ->raw('Este é um email de teste do microserviço. Se chegou, a configuração SMTP está correta!', function ($message) use ($destinatario) {
                $message->to($destinatario)
                        ->subject('✅ Teste de Envio - Microserviço de Email');
            });

        return response()->json([
            'success' => true,
            'message' => "Email de teste enviado para: {$destinatario}"
        ]);
    }
}