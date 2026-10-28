<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;    //true = não necessita estar logado
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => [$this->isMethod('post') ? 'prohibited' : 'nullable', 'string', 'min:6', 'confirmed'],
            'active'   => [$this->isMethod('post') ? 'nullable' : 'required', 'in:Y,N'],
            'status'   => [$this->isMethod('post') ? 'prohibited' : 'required', 'in:invited,pending,active,suspended'],

            'name' => 'required|string|min:6',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->id,
            'phone' => 'string|min:10|max:15|nullable|unique:users,phone,' . $this->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    // transformations that need to be done before validation
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => ucwords(trim(strtolower($this->name))),
            'email' => trim(strtolower($this->email)),
            'active' => $this->boolean('active') ? 'Y' : 'N',   // <- corrige o checkbox
        ]);
    }      
}
