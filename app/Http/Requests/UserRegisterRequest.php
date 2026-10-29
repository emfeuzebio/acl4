<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $min = (int) env('PASSWORD_MIN_LENGTH', 6);

        return [
            'name'      => 'required|string|min:5|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'password'  => 'required|string|min:' . $min . '|confirmed|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{' . $min . ',}$/',
            'phone'     => 'nullable|string|min:10|max:15|unique:users,phone',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active'    => ['prohibited'],   // backend define 'N'
            'status'    => ['prohibited'],   // backend define 'pending'
            'system_id' => ['required', 'integer', 'exists:acl_systems,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'O nome é obrigatório.',
            'name.min'           => 'O nome deve ter pelo menos 5 caracteres.',
            'email.required'     => 'O e-mail é obrigatório.',
            'email.email'        => 'Formato de e-mail inválido.',
            'email.unique'       => 'Este e-mail já está cadastrado.',
            'password.required'  => 'A senha é obrigatória.',
            'password.min'       => 'A senha deve ter pelo menos ' . env('PASSWORD_MIN_LENGTH', 6) . ' caracteres.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.regex'     => 'A senha deve conter ao menos uma letra maiúscula, um número e um caractere especial.',
            'phone.min'          => 'O telefone deve ter pelo menos 10 caracteres.',
            'phone.unique'       => 'Este telefone já está cadastrado.',
            'system_id.required' => 'O sistema de origem é obrigatório.',
            'system_id.exists'   => 'Sistema de origem inválido.',
            'active.prohibited'  => 'O campo active é definido pelo sistema.',
            'status.prohibited'  => 'O campo status é definido pelo sistema.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => ucwords(trim(strtolower((string) $this->name))),
            'email' => trim(strtolower((string) $this->email)),
        ]);
    }
}