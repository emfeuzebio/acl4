<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailTesteController extends Controller
{
    public function enviar()
    {

        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');        

        $destinatario = 'emfeuzebio72@gmail.com';
        
        $config = [
            'host'      => config('mail.mailers.legado.host'),
            'port'      => config('mail.mailers.legado.port'),
            'encryption'=> config('mail.mailers.legado.encryption'),
            'username'  => config('mail.mailers.legado.username'),
            'from'      => config('mail.from.address'),
        ];

        try {
            Mail::mailer('legado')
                ->raw('Teste de email do microserviço.', function ($message) use ($destinatario) {
                    $message->to($destinatario)
                            ->subject('Teste de Envio')
                            ->from('guillon@das.febnet.org.br', 'Guillon Ribeiro');
                });

            return response()->json([
                'success' => true,
                'config'  => $config,
                'message' => "Email enviado com sucesso para {$destinatario}!"
            ]);

        } catch (\Exception $e) {
            Log::error('Erro email: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'config'  => $config
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