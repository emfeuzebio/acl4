<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACL_SystemsProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_system_profile')->insert([

            // 1-Administrator has on first and all systems
            [
                'system_id' => '1',
                'profile_id' => '1',
            ],
            // 2-Manager has has on first and all systems
            [
                'system_id' => '1',
                'profile_id' => '2',
            ],
            // 3-User has has on first and all systems
            [
                'system_id' => '1',
                'profile_id' => '3',
            ],
        ]);
    }
}
