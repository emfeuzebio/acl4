<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACL_ProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_profiles')->insert([
            [
                'system_id' => '1',
                'name' => 'Administrator',
                'acronym' => 'Admin',
                'description' => 'Administration of the entire system including user access control. Unrestricted access to all functions',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'system_id' => '1',
                'name' => 'Manager',
                'acronym' => 'Mng',
                'description' => 'Manage one or more system modules',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'system_id' => '1',
                'name' => 'User',
                'acronym' => 'Usr',
                'description' => 'Users of one or more system modules',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Adicione mais registros conforme necessário
        ]);
    }
}
