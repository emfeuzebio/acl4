<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isStore = $this->isMethod('post');
        // $id = $isStore ? null : $this->input('id');
        $id = $isStore
            ? null
            : ($this->input('id') ?? $this->route('user') ?? $this->route('id'));        

        return [
            'password' => [$isStore ? 'prohibited' : 'nullable', 'string', 'min:6', 'confirmed'],
            'active'   => [$isStore ? 'nullable' : 'required', 'in:Y,N'],
            'status'   => [$isStore ? 'prohibited' : 'required', 'in:invited,pending,active,suspended'],

            'name'  => 'required|string|min:6',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['nullable', 'string', 'min:10', 'max:15', Rule::unique('users', 'phone')->ignore($id)],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [
            'name'  => ucwords(trim(strtolower((string) $this->name))),
            'email' => trim(strtolower((string) $this->email)),
        ];

        // Só normaliza 'active' quando vier (update). No store o backend define 'Y'.
        if ($this->has('active')) {
            $v = $this->input('active');
            if (is_array($v)) {
                $v = end($v); // se vier duplicado (on + Y), pega o último
            }
            $merge['active'] = in_array($v, ['Y', 'N'], true)
                ? $v
                : ($this->boolean('active') ? 'Y' : 'N');
        }

        $this->merge($merge);
    }
}