<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     * Como é uma API pública, pode deixar `true`.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Define as regras de validação para o registro de usuários.
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:5',
            // 'email' => 'required|string|email|max:255|unique:users,email',
            // 'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            // 'email' => 'required|string|email|max:255|unique:users,email' . $this->id,
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    /**
     * Mensagens de erro personalizadas para as validações.
     */
    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 5 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Formato de e-mail inválido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'As senhas não coincidem.',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (User::where('email', $this->email)->exists()) {
                $validator->errors()->add('email', 'Este e-mail já está cadastrado.');
            }

            // $validator->errors()->add('email', 'Este e-mail já está cadastrado.');
        });
    }    
}
