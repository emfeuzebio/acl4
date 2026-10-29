<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name'  => 'required|string|min:6|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|min:10|max:15|unique:users,phone,' . $id,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'O nome é obrigatório.',
            'name.min'       => 'O nome deve ter pelo menos 6 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'Formato de e-mail inválido.',
            'email.unique'   => 'Este e-mail já está cadastrado.',
            'phone.unique'   => 'Este telefone já está cadastrado.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => ucwords(trim(strtolower((string) $this->name))),
            'email' => trim(strtolower((string) $this->email)),
            'phone' => $this->filled('phone') ? $this->phone : null,
        ]);
    }
}