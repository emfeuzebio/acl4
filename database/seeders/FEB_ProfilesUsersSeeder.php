<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_ProfilesUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_profile_user')->insert([

            // Administrator has all profiles
            [
                'user_id' => '1',
                'profile_id' => '1',
            ],
            [
                'user_id' => '1',
                'profile_id' => '2',
            ],
            [
                'user_id' => '1',
                'profile_id' => '3',
            ],

            // Manager has only manager profile
            [
                'user_id' => '2',
                'profile_id' => '2',
            ],

            // User has only user profile
            [
                'user_id' => '3',
                'profile_id' => '3',
            ],

            // Adicione mais registros conforme necessário
            // password: 12345678
        ]);
    }
}
