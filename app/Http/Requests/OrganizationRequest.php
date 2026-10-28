<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    public function authorize()
    {
        // return true;                // padrão é true = não necessita estar logado
        // return auth()->user() && auth()->user()->role === 'admin';  // Apenas usuários admin    
        return auth()->check();     // Verifica se o usuário está autenticado
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:6|unique:acl_organizations,name,' . $this->id,
            'acronym' => 'required|string|min:3|max:20|unique:acl_organizations,acronym,' . $this->id,
            'description' => 'required|min:6',            
            'active' => ['required','in:"Y","N"'],
        ];
    }
}
