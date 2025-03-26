<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACL_SystemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_systems')->insert([
            [
                // 'organization_id' => '1',
                'name' => 'Access Control List JWT',
                'acronym' => 'ALC JWT',
                'description' => 'Access Control List issues JWT Tokens',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Adicione mais registros conforme necessário
        ]);
    }
}
