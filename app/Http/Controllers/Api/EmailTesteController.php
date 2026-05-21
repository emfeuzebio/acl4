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

        try {
            Mail::raw('Teste de email do microserviço.', function ($message) use ($destinatario) {
                $message->to($destinatario)
                        ->subject('Teste de Envio');
            });

            return response()->json([
                'success' => true,
                'message' => "Email enviado com sucesso para {$destinatario}!"
            ]);

        } catch (\Exception $e) {
            Log::error('Erro email: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}