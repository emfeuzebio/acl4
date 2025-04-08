<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VeiculoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            "descricao" => $this->descricao,
            "tipo" => $this->tipo,
            "marca_modelo" => $this->marca_modelo,
            "capacidade" => $this->capacidade,
            "motorista" => $this->motorista,
            "telefone" => $this->telefone,
            "observacao" => $this->observacao,
            "ativo" => $this->ativo,
            // "preco_diaria" => 'R$ ' . number_format($this->preco_diaria, 2, ',', '.'),
            // "created_at" => $this->created_at,
            // "updated_at" => $this->updated_at,
            // "criado_em" => $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null,
            // "atualizado_em" => $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null,
            // "atualizado_em" => $this->updated_at ? Carbon::parse($this->updated_at)->format('d/m/Y H:i:s') : null,
            // "viagens" => $this->viagens,
        ];
    }
}
