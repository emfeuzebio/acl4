<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isStore = $this->isMethod('post');

        return [
            'password' => [$isStore ? 'prohibited' : 'nullable', 'string', 'min:6', 'confirmed'],
            'active'   => [$isStore ? 'nullable' : 'required', 'in:Y,N'],
            'status'   => [$isStore ? 'prohibited' : 'required', 'in:invited,pending,active,suspended'],

            'name'  => 'required|string|min:6',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->id,
            'phone' => 'nullable|string|min:10|max:15|unique:users,phone,' . $this->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [
            'name'  => ucwords(trim(strtolower((string) $this->name))),
            'email' => trim(strtolower((string) $this->email)),
        ];

        if ($this->has('active')) {
            $v = $this->input('active');
            if (is_array($v)) {
                $v = end($v);
            }
            $merge['active'] = in_array($v, ['Y', 'N'], true)
                ? $v
                : ($this->boolean('active') ? 'Y' : 'N');
        }

        $this->merge($merge);
    }
}