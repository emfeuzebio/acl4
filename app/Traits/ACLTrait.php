<?php

namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait ACLTrait
{
    /**
     * Retorna um array com todas as habilidades (ações/rotas autorizadas) do User logado as quais foram definidas com Gate::define()
     */
    public function getAbilities($entity)
    {
        $authorizedRoutes = [];

        // retrieve a Collection of closures (Anonymous Functions) in this case that expects a User as a parameter: "user.index" => Closure(User $user)
        foreach (Gate::abilities() as $ability => $callback) {
            if (Gate::allows($ability)) {
                if (stripos($ability, $entity) !== false) {
                    $authorizedRoutes[] = $ability;  // Adds the ability to the array if the user has permission
                }
            }
        }        

        return $authorizedRoutes;
    }         

}
