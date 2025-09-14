<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            // 'organization_id' => 'required|exists:acl_organizations,id',    // Mandatory and existing foreign key in the acl organizations table
            'name' => 'required|string|min:6',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->id,
            'password' => 'nullable|string|min:6|confirmed',
            // 'phone' => 'string|min:10|max:15|nullable|unique:users,phone,' . $this->id,

            'phone' => ['string','min:10','max:15','nullable',Rule::unique('users', 'phone')->ignore($this->route('id'))
                    ],            

            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => ['required','in:"Y","N"'],
        ];
    }

    // transformations that need to be done before validation
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => ucwords(trim(strtolower($this->name))),
            'email' => trim(strtolower($this->email)),
        ]);
    }      
}
