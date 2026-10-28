<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AcceptInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // A autorização real é o token válido, checado no InviteController.
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(10)->mixedCase()->numbers()->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required'  => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min'       => 'A senha deve ter pelo menos 10 caracteres.',
        ];
    }
}