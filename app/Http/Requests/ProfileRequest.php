<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return true;                // padrão é true = não necessita estar logado
        return auth()->check();     // Verifica se o usuário está autenticado    
        // return auth()->user() && auth()->user()->role === 'admin';  // Apenas usuários admin    
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // A chave estrangeira obrigatória e existente na tabela acl_organizacaos
            'system_id' => 'required|exists:acl_profiles,id',  
            // O nome não pode repetir na mesma organização (organizacao_id,nome são únicos)
            'name' => 'required|string|min:6|unique:acl_profiles,name,' . $this->id . ',id,system_id,' . $this->system_id,
            // A sigla não pode repetir na mesma organização (organizacao_id,sigla são únicas)
            'acronym' => 'required|string|min:3|max:20|unique:acl_profiles,acronym,' . $this->id . ',id,system_id,' . $this->system_id,
            'description' => 'required|min:6',            
            'active' => ['required','in:"Y","N"'],
        ];
    }
}
