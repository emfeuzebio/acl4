<?php

namespace App\Repositories;

use App\Models\Entity;
use App\Models\User;
use App\Models\Profile;
use App\Models\Organization;
use App\Models\System;
use Carbon\Carbon;

class DashboardRepository
{
    public function getTotalUsers(): int
    {
        return User::count();
    }

    public function getTotalProfiles(): int
    {
        return Profile::count();
    }

    public function getTotalEntities(): int
    {
        return Entity::count();
    }

    public function getTotalOrganizations(): int
    {
        return Organization::count();
    }

    public function getTotalSystems(): int
    {
        return System::count();
    }

    public function getNewUsersLast30Days(): int
    {
        return User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
    }
}
