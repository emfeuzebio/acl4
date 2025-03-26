<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_OrganizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_organizations')->insert([
            [
                'name' => 'FEB Guillon',
                'acronym' => 'FEB NEGR',
                'description' => 'Núcleo Espírita Guillon Ribeiro',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'FEB Lar Frederico Figner',
                'acronym' => 'FEB LFF',
                'description' => 'Lar Frederico Figner',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // add mora registers ir necessary
        ]);
    }
}
