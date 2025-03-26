<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_UsersSeeder extends Seeder
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
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'organization_id' => '1',       // default organization
                'active' => 'Y',
                'name' => 'Moroni',
                'email' => 'moroni@mail.com',
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'organization_id' => '1',       // default organization
                'active' => 'Y',
                'name' => 'Sandra',
                'email' => 'sandra@mail.com',
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // password: 12345678
        ]);
    }
}
