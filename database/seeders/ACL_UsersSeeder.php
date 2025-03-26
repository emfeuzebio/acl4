<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACL_UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                // 'organization_id' => '1',       // default organization
                'active' => 'Y',
                'name' => 'Administrator',
                'email' => 'administrator@mail.com',
                // 'password' => '$2y$12$h6VXebJquAIqW7emOThnMuw9n8vv61acD3nyPmoHc4nq6E9S1ceiy',
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'organization_id' => '1',       // default organization
                'active' => 'Y',
                'name' => 'Manager',
                'email' => 'manager@mail.com',
                // 'password' => '$2y$12$h6VXebJquAIqW7emOThnMuw9n8vv61acD3nyPmoHc4nq6E9S1ceiy',
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'organization_id' => '1',       // default organization
                'active' => 'Y',
                'name' => 'User',
                'email' => 'user@mail.com',
                // 'password' => '$2y$12$h6VXebJquAIqW7emOThnMuw9n8vv61acD3nyPmoHc4nq6E9S1ceiy',
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // password: 12345678
        ]);
    }
}
