<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_SystemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_systems')->insert([
            [
                // 'organization_id' => '1',
                'name' => 'FEB Guillon',
                'acronym' => 'FEB NEGR',
                'description' => 'Sistema de Gestão do NEGR',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'organization_id' => '1',
                'name' => 'FEB Eventos',
                'acronym' => 'FEB Evt',
                'description' => 'Sistema de Gestão Eventos da FEB',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
