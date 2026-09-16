<?php

namespace App\EmailService\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to'       => 'required|email|max:255',
            'to_nome'  => 'nullable|string|max:255',
            'assunto'  => 'required|string|max:255',
            'template' => [
                'required',
                'string',
                Rule::in(config('email_service.allowed_templates', [])),
            ],
            'dados'    => 'nullable|array',
            'reply_to' => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'to.required'       => 'Destinatário é obrigatório.',
            'to.email'          => 'Destinatário deve ser um e-mail válido.',
            'assunto.required'  => 'Assunto é obrigatório.',
            'template.required' => 'Template é obrigatório.',
            'template.in'       => 'Template não permitido.',
        ];
    }
}