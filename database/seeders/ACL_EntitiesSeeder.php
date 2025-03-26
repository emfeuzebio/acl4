<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACL_EntitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_entities')->insert([
            [
                // 'system_id' => '1',
                'model' => 'Organization',
                'table' => 'acl_organizations',
                'description' => 'Stores the list of Organizations',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                // 'sistema_id' => '1',
                'model' => 'System',
                'table' => 'acl_systems',
                'description' => 'Stores the list of Systems',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'system_id' => '1',
                'model' => 'Entity',
                'table' => 'acl_entities',
                'description' => 'Stores the list of Entities',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'system_id' => '1',
                'model' => 'Actions',
                'table' => 'acl_actions',
                'description' => 'Stores the list of Actions',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],           
            [
                // 'system_id' => '1',
                'model' => 'Authorization',
                'table' => 'acl_authorizations',
                'description' => 'Stores the list of Authorizations',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'system_id' => '1',
                'model' => 'User',
                'table' => 'users',
                'description' => 'Stores the list of Users',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],            
            [
                // 'system_id' => '1',
                'model' => 'Profile',
                'table' => 'acl_profiles',
                'description' => 'Stores the list of Access Profiles',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],            
            // Adicione mais registros conforme necessário
        ]);
    }
}
