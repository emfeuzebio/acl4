<?php

namespace App\EmailService\Http\Controllers;

use App\Http\Controllers\Controller;
use App\EmailService\Http\Requests\SendEmailRequest;
use App\EmailService\Mail\GenericMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailController extends Controller
{
    public function send(SendEmailRequest $request)
    {
        $data       = $request->validated();
        $consumer   = $request->attributes->get('consumer_config');
        $consumerId = $request->attributes->get('consumer_id');

        try {
            Mail::to($data['to'], $data['to_nome'] ?? null)
                ->send(new GenericMailable(
                    template:    $data['template'],
                    assunto:     $data['assunto'],
                    dados:       $data['dados'] ?? [],
                    fromAddress: $consumer['from_address'] ?? null,
                    fromName:    $consumer['from_name']    ?? null,
                    replyTo:     $data['reply_to']         ?? null,
                ));

            Log::info('EmailService: enviado', [
                'consumer' => $consumerId,
                'to'       => $data['to'],
                'template' => $data['template'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email enviado',
                'to'      => $data['to'],
            ], 200);

        } catch (Throwable $e) {
            Log::error('EmailService: falha no envio', [
                'consumer' => $consumerId,
                'to'       => $data['to'],
                'template' => $data['template'],
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Falha ao enviar email',
                'detalhe' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}