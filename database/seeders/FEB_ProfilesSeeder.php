<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_ProfilesSeeder extends Seeder
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
                'name' => 'Gerente de Transportes',
                'acronym' => 'Ger Trnsp',
                'description' => 'Gerencia os Transportes',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'system_id' => '1',
                'name' => 'Gerente de Doações',
                'acronym' => 'Ger Doa',
                'description' => 'Gerente de Doações',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Adicione mais registros conforme necessário
        ]);
    }
}
