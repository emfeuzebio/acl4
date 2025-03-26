<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_OrganizationsUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_organization_user')->insert([

            // Organization 1
            // 1-Administrator User on Organization
            [
                'organization_id' => '1',
                'user_id' => '1',
            ],
            // [
            //     'organization_id' => '2',
            //     'user_id' => '1',
            // ],
            // 2-User User on Organization
            [
                'organization_id' => '1',
                'user_id' => '2',
            ],
            // 3-User User on Organization
            [
                'organization_id' => '1',
                'user_id' => '3',
            ],
        ]);
    }
}
