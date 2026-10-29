<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcceptInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $min = (int) env('PASSWORD_MIN_LENGTH', 6);

        return [
            'password' => 'required|string|min:' . $min . '|confirmed|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{' . $min . ',}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required'  => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min'       => 'A senha deve ter pelo menos ' . env('PASSWORD_MIN_LENGTH', 6) . ' caracteres.',
            'password.regex'     => 'A senha deve conter ao menos uma letra maiúscula, um número e um caractere especial.',
        ];
    }
}