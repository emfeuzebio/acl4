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
            'from'      => config('mail.mailers.legado.from.address'),
        ];

        try {
            // Mail::mailer('legado')
            // Mail::mailer('legado')
            //     ->raw('Teste de email do microserviço.', function ($message) use ($destinatario) {
            //         $message->to($destinatario)
            //                 ->subject('Teste de Envio');
            //     });

            Mail::raw('Teste de email do microserviço.', function ($message) use ($destinatario) {
                $message->to($destinatario)
                        ->subject('Teste de Envio');
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