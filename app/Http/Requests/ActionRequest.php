<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ActionRequest extends FormRequest
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
            'entity_id' => 'required|exists:acl_entities,id',
            // O nome não pode repetir na mesma organização (organizacao_id,nome são únicos)
            'action' => 'required|string|min:4|max:30|unique:acl_actions,action,' . $this->id . ',id,entity_id,' . $this->entity_id,
            // A sigla não pode repetir na mesma organização (organizacao_id,sigla são únicas)
            'route' => 'required|string|min:6|max:30|unique:acl_actions,route,' . $this->id . ',id,entity_id,' . $this->entity_id,
            'description' => 'required|min:6',            
        ];
    }

    // transformations that need to be done before validation
    protected function prepareForValidation()
    {
        $route = preg_replace('/[^a-zA-Z.]/', '', Str::ascii($this->route));
        // $route = strtolower(preg_replace('/[^a-zA-Z.]/', '', Str::ascii($this->route)));
        // $route = Str::singular($route);

        $this->merge([
            'action' => ucwords(trim($this->action)),
            // 'action' => ucwords(trim(strtolower($this->action))),
            // 'route' => trim(strtolower($this->route)),
            'route' => trim($route),
            'description' => $this->description,
            // 'description' => ucwords(trim(strtolower($this->description))),
        ]);
    }    
}
