<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
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
            "user_id" => $this->user_id,
            "system_id" => $this->system_id,
            "ip" => $this->ip,
            "status" => $this->status,
            "browser" => $this->browser,
            "created_at" => $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null,
            "updated_at" => $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null,
            "expires_at" => $this->expires_at ? $this->expires_at->format('d/m/Y H:i:s') : null,
            'user' => $this->user,              // inclui o relacionamento 'sistemas'
            // "atualizado_em" => $this->updated_at ? Carbon::parse($this->updated_at)->format('d/m/Y H:i:s') : null,
            // 'sistemas' => SistemaResource::collection($this->whenLoaded('sistemas')), // Aplica um recurso para a relação 'sistemas'
            // "DT_RowId" => $this->id,
            // 'routes' => $this->getAuthorizedRoutes(),
            // 'autorizacoes' => $this->getAuthorizedActions(), // ATENCAO MUITO LENTO, para cada linha um getAuthorizedActions() que faz 45 consultas ao BD
        ];
    }
}
