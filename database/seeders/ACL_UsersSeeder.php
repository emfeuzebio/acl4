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
                'active' => 'Y',
                'name' => 'Administrator',
                'email' => 'administrator@mail.com',
                'phone' => '55 (61) 90000-0000',
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'active' => 'Y',
                'name' => 'Manager',
                'email' => 'manager@mail.com',
                'phone' => '55 (61) 90000-0001',
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'active' => 'Y',
                'name' => 'User',
                'email' => 'user@mail.com',
                'phone' => '55 (61) 90000-0002',
                'password' => bcrypt('12345678'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
