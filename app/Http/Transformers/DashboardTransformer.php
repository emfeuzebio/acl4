<?php

namespace App\Transformers;

class DashboardTransformer
{
    public static function transform(array $data): array
    {
        return [
            'users' => [
                'total' => $data['total_users'],
                'new_last_30_days' => $data['new_users_last_30d'],
            ],
            'profiles' => [
                'total' => $data['total_profiles'],
            ],
            'organizations' => [
                'total' => $data['total_organizations'],
            ],
            'systems' => [
                'total' => $data['total_systems'],
            ]
        ];
    }
}
