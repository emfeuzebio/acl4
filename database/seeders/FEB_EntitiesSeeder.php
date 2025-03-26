<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_EntitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_entities')->insert([
            // Entidades Básicas para o funcionamento da ACL
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
            // Entidades Específicas do FEB Guillon
            [
                // 'system_id' => '1',
                'model' => 'Familias',
                'table' => 'das_familias',
                'description' => 'Armazena o cadastro de Famílias',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                // 'system_id' => '1',
                'model' => 'Pessoas',
                'table' => 'das_pessoas',
                'description' => 'Armazena o cadastro de Pessoas das Familias',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],            
            [
                // 'system_id' => '1',
                'model' => 'Pedidos',
                'table' => 'das_pedidos',
                'description' => 'Armazena o cadastro de Pedidos de Doações das Pessoas',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],            
            // Entidades Específicas do FEB Eventos
            [
                // 'system_id' => '1',
                'model' => 'Veículos',
                'table' => 'evt_veiculos',
                'description' => 'Armazena o cadastro de Veículos',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                // 'system_id' => '1',
                'model' => 'Rotas',
                'table' => 'evt_rotas',
                'description' => 'Armazena o cadastro de Rotas de Transportes',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],            
            [
                // 'system_id' => '1',
                'model' => 'Viagens',
                'table' => 'evt_viagens',
                'description' => 'Armazena o cadastro de Viagens efetuadas por Veículos em Rotas',
                'active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],            

        ]);
    }
}
