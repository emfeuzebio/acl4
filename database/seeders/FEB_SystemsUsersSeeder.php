<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_SystemsUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_system_user')->insert([

            // 1-Administrator has on first and all systems
            [
                'user_id' => '1',
                'system_id' => '1',
            ],
            [
                'user_id' => '1',
                'system_id' => '2',
            ],
            // 2-Manager has has on first and all systems
            [
                'user_id' => '2',
                'system_id' => '2',
            ],
            // 3-User has has on first and all systems
            [
                'user_id' => '3',
                'system_id' => '1',
            ],
        ]);
    }
}
