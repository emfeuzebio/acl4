<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailTesteController extends Controller
{
    public function enviar()
    {
        try {
            $destinatario = 'emfeuzebio72@gmail.com';

            Log::info('Tentando enviar email', [
                'host' => config('mail.mailers.legado.host'),
                'port' => config('mail.mailers.legado.port'),
                'username' => config('mail.mailers.legado.username'),
                'destinatario' => $destinatario
            ]);            

            Mail::mailer('legado')
                ->raw('Teste de email do microserviço.', function ($message) use ($destinatario) {
                    $message->to($destinatario)
                            ->subject('Teste de Envio');
                });

            return response()->json([
                'success' => true,
                'message' => "Email enviado para: {$destinatario}"
            ]);

        } catch (\Exception $e) {
            // Registra o erro no log do Laravel
            Log::error('Erro ao enviar email: ' . $e->getMessage());
            
            // Retorna o erro como JSON para debug
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Mail;

// class EmailTesteController extends Controller
// {
//     public function enviar()
//     {
//         // Destinatário do teste
//         $destinatario = 'emfeuzebio72@gmail.com'; // ← Troque pelo seu email pessoal

//         // Enviar usando o mailer 'legado'
//         Mail::mailer('legado')
//             ->raw('Este é um email de teste do microserviço. Se chegou, a configuração SMTP está correta!', function ($message) use ($destinatario) {
//                 $message->to($destinatario)
//                         ->subject('✅ Teste de Envio - Microserviço de Email');
//             });

//         return response()->json([
//             'success' => true,
//             'message' => "Email de teste enviado para: {$destinatario}"
//         ]);
//     }
// }