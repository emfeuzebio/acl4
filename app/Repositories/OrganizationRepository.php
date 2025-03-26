<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\User;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function all(): Collection
    {
        return Organization::all();
    }

    public function find(int $id): ?Organization
    {
        return Organization::find($id);
    }

    public function create(Request $request): Organization 
    {
        $organization = Organization::Create(
            $request->only(['name', 'acronym', 'description', 'active'])
        );  

        if (!$organization) {
            throw new Exception('Exception-OrganizationRepository: A Organization NÃO foi inserida.');
        }
        
        return $organization;
    }    

    public function update(int $id, array $data): ?Organization
    {
        $order = Organization::find($id);
        if ($order) {
            $order->update($data);
            return $order;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $order = Organization::find($id);
        if ($order) {
            return $order->delete();
        }
        return false;
    }



    public function checkUser($userId): User
    {
        $user = User::find($userId);    

        if (! $user) {
            throw new Exception('Exception-OrganizationRepository: Usuário NÃO existe.');
        }   
        
        return $user;
    } 
    
    public function checkProfile(User $user)
    {
        $userProfiles = $user->hasProfiles();

        if (! $userProfiles) {
            throw new Exception('Exception-OrganizationRepository: O Usuário NÃO tem Perfil de Acesso necessário.');
        }
        
        return $userProfiles;
    }    
}
