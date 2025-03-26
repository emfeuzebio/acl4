<?php

namespace App\Repositories\Interfaces;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

interface OrganizationRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Organization;
    public function create(Request $request): Organization;
    public function update(int $id, array $data): ?Organization;
    public function delete(int $id): bool;

    public function checkUser(int $id): ?User;
    public function checkProfile(User $user);
}