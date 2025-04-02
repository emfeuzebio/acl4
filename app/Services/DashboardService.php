<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    protected $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardData(): array
    {
        return [
            'total_users'        => $this->dashboardRepository->getTotalUsers(),
            'total_profiles'     => $this->dashboardRepository->getTotalProfiles(),
            'total_organizations'=> $this->dashboardRepository->getTotalOrganizations(),
            'total_systems'      => $this->dashboardRepository->getTotalSystems(),
            'total_entities'     => $this->dashboardRepository->getTotalEntities(),
            'new_users_last_30d' => $this->dashboardRepository->getNewUsersLast30Days(),
        ];
    }
}
