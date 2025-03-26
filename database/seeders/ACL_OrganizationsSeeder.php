<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACL_OrganizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_organizations')->insert([
            [
                'name' => 'Fisrt Organization',
                'acronym' => '1st Org',
                'description' => 'Description of the First Organization',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Second Organization',
                'acronym' => '2nd Org',
                'description' => 'Description of the Second Organization',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // add mora registers ir necessary
        ]);
    }
}
