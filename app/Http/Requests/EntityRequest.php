<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntityRequest extends FormRequest
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
            // 'system_id' => 'required|exists:acl_entities,id',  
            // O nome não pode repetir na mesma organização (organizacao_id,nome são únicos)
            // 'model' => 'required|string|min:4|unique:acl_entities,model,' . $this->id . ',id,system_id,' . $this->system_id,
            'model' => 'required|string|min:4|unique:acl_entities,model,' . $this->id,
            // A sigla não pode repetir na mesma organização (organizacao_id,sigla são únicas)
            // 'table' => 'required|string|min:3|max:20|unique:acl_entities,table,' . $this->id . ',id,system_id,' . $this->system_id,
            'table' => 'required|string|min:3|max:20|unique:acl_entities,table,' . $this->id,
            'description' => 'required|min:6',
            'active' => ['required','in:"Y","N"'],
        ];
    }
}
