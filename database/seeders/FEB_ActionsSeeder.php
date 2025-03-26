<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_ActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl_actions')->insert([

            // 1 Organizations - Actions to Authorizations table: Let's populate the Actions table
            [
                'entity_id' => '1',                 // Entity foreign key
                'action' => 'List Organizations',
                'route' => 'organization.index',
                'description' => 'List Organizations',
            ],
            [
                'entity_id' => '1',                 // Entity foreign key
                'action' => 'Show Organizations',
                'route' => 'organization.show',
                'description' => 'Show Organizations',
            ],
            [
                'entity_id' => '1',                 // Entity foreign key    
                'action' => 'Insert Organizations',
                'route' => 'organization.store',
                'description' => 'Insert Organizations',
            ],
            [
                'entity_id' => '1',                 // Entity foreign key    
                'action' => 'Update Organizations',
                'route' => 'organization.update',
                'description' => 'Update Organizations',
            ],
            [
                'entity_id' => '1',                 // Entity foreign key    
                'action' => 'Delete Organizations',
                'route' => 'organization.destroy',
                'description' => 'Delete Organizations',
            ],                


            // 2 Systems - Actions to Authorizations table: Let's populate the Actions table
            [
                'entity_id' => '2',                 // Entity foreign key
                'action' => 'List Systems',
                'route' => 'system.index',
                'description' => 'List Systems',
            ],
            [
                'entity_id' => '2',                 // Entity foreign key
                'action' => 'Show Systems',
                'route' => 'system.show',
                'description' => 'Show Systems',
            ],
            [
                'entity_id' => '2',                 // Entity foreign key    
                'action' => 'Insert Systems',
                'route' => 'system.store',
                'description' => 'Insert Systems',
            ],
            [
                'entity_id' => '2',                 // Entity foreign key    
                'action' => 'Update Systems',
                'route' => 'system.update',
                'description' => 'Update Systems',
            ],
            [
                'entity_id' => '2',                 // Entity foreign key    
                'action' => 'Delete Systems',
                'route' => 'system.destroy',
                'description' => 'Delete Systems',
            ],              
            
            // 3 Entities - Actions to Authorizations table: Let's populate the Actions table
            [
                'entity_id' => '3',                 // Entity foreign key
                'action' => 'List Entities',
                'route' => 'entity.index',
                'description' => 'List Entities',
            ],
            [
                'entity_id' => '3',                 // Entity foreign key
                'action' => 'Show Entities',
                'route' => 'entity.show',
                'description' => 'Show Entities',
            ],
            [
                'entity_id' => '3',                 // Entity foreign key    
                'action' => 'Insert Entities',
                'route' => 'entity.store',
                'description' => 'Insert Entities',
            ],
            [
                'entity_id' => '3',                 // Entity foreign key    
                'action' => 'Update Entities',
                'route' => 'entity.update',
                'description' => 'Update Entities',
            ],
            [
                'entity_id' => '3',                 // Entity foreign key    
                'action' => 'Delete Entities',
                'route' => 'entity.destroy',
                'description' => 'Delete Entities',
            ],                
            [
                'entity_id' => '3',                 // Entity foreign key    
                'action' => "List Entity's Authorizations",
                'route' => 'entity.listAuthorzs',
                'description' => "List Entity's Authorizations",
            ],                
            [
                'entity_id' => '3',                 // Entity foreign key    
                'action' => "List Entity's Actions",
                'route' => 'entity.listActions',
                'description' => "List Entity's Actions",
            ],                
            [
                'entity_id' => '3',                 // Entity foreign key    
                'action' => "List Entities Grants",
                'route' => 'entity.listEntities',
                'description' => "List Entities Grants",
            ],                


            // 4 Actions - Actions to Actions table: Let's populate the Actions table
            [
                'entity_id' => '4',                 // Entity foreign key
                'action' => 'List Actions',
                'route' => 'action.index',
                'description' => 'List Actions',
            ],
            [
                'entity_id' => '4',                 // Entity foreign key
                'action' => 'Show Action',
                'route' => 'action.show',
                'description' => 'Show Action',
            ],
            [
                'entity_id' => '4',                 // Entity foreign key    
                'action' => 'Insert Action',
                'route' => 'action.store',
                'description' => 'Insert Action',
            ],
            [
                'entity_id' => '4',                 // Entity foreign key    
                'action' => 'Update Action',
                'route' => 'action.update',
                'description' => 'Update Action',
            ],
            [
                'entity_id' => '4',                 // Entity foreign key    
                'action' => 'Delete Action',
                'route' => 'action.destroy',
                'description' => 'Delete Action',
            ],

            // 5 Authorizations - Actions to Authorizations table: Let's populate the Actions table
            [
                'entity_id' => '5',                 // Entity foreign key
                'action' => 'List Authorizations',
                'route' => 'authorization.index',
                'description' => 'List Authorizations',
            ],
            [
                'entity_id' => '5',                 // Entity foreign key
                'action' => 'Show Authorization',
                'route' => 'authorization.show',
                'description' => 'Show Authorization',
            ],
            [
                'entity_id' => '5',                 // Entity foreign key    
                'action' => 'Insert Authorization',
                'route' => 'authorization.store',
                'description' => 'Insert Authorization',
            ],
            [
                'entity_id' => '5',                 // Entity foreign key    
                'action' => 'Update Authorization',
                'route' => 'authorization.update',
                'description' => 'Update Authorization',
            ],
            [
                'entity_id' => '5',                 // Entity foreign key    
                'action' => 'Delete Authorization',
                'route' => 'authorization.destroy',
                'description' => 'Delete Authorization',
            ],            

            // 6 Users - Actions to Users table: Let's populate the Actions table
            [
                'entity_id' => '6',                 // Entity foreign key
                'action' => 'List Users',
                'route' => 'user.index',
                'description' => 'List Users',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key
                'action' => 'Show User',
                'route' => 'user.show',
                'description' => 'Show User',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Insert User',
                'route' => 'user.store',
                'description' => 'Insert User',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Update User',
                'route' => 'user.update',
                'description' => 'Update User',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Delete User',
                'route' => 'user.destroy',
                'description' => 'Delete User',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Update User Profile',
                'route' => 'user.updateProfile',
                'description' => 'Update User Profile',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => "List User's Profile/Role",
                'route' => 'user.listRoles',
                'description' => 'List of Profile/Role granted to User',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => "List User's Systems",
                'route' => 'user.listSystems',
                'description' => 'List of system granted to User',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => "List User's Active Authorz",
                'route' => 'user.listActiveAuth',
                'description' => "List Active User's Authorizations",
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => "List User's Organizations",
                'route' => 'user.listOrganiz',
                'description' => 'List of Organizations granted to User',
            ],
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Grant/Revoke Profile/Role',
                'route' => 'user.toggleRole',
                'description' => 'Grant/Revoke User Access Profile/Role',
            ],    
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Grant/Revoke System',
                'route' => 'user.toggleSystem',
                'description' => 'Grant/Revoke User Access System',
            ],    
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Grant/Revoke Authorizations',
                'route' => 'user.toggleAuthorz',
                'description' => 'Grant/Revoke Authorization to User in an Action',
            ],    
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Grant/Revoke Organizations',
                'route' => 'user.toggleOrganiz',
                'description' => 'Grant/Revoke Organizations to User',
            ],    
            [
                'entity_id' => '6',                 // Entity foreign key    
                'action' => 'Reset Database',
                'route' => 'user.refreshDB',
                'description' => 'Execute Reset DataBase (Truncate, Migrate and Seed) the DataBase to initial charge',
            ],    
            
            // 7 Profiles - Actions to Profiles table: Let's populate the Actions table
            [
                'entity_id' => '7',                 // Entity foreign key
                'action' => 'List Profiles',
                'route' => 'profile.index',
                'description' => 'List Profiles',
            ],
            [
                'entity_id' => '7',                 // Entity foreign key
                'action' => 'Show Profile',
                'route' => 'profile.show',
                'description' => 'Show Profile',
            ],
            [
                'entity_id' => '7',                 // Entity foreign key    
                'action' => 'Insert Profile',
                'route' => 'profile.store',
                'description' => 'Insert Profile',
            ],
            [
                'entity_id' => '7',                 // Entity foreign key    
                'action' => 'Update Profile',
                'route' => 'profile.update',
                'description' => 'Update Profile',
            ],
            [
                'entity_id' => '7',                 // Entity foreign key    
                'action' => 'Delete Profile',
                'route' => 'profile.destroy',
                'description' => 'Delete Profile',
            ],     
            [
                'entity_id' => '7',                 // Entity foreign key    
                'action' => 'Grant/Revoke Entity',
                'route' => 'profile.grantEntity',
                'description' => 'Grant/Revoke Entity to Profile',
            ],              
            [
                'entity_id' => '7',                 // Entity foreign key    
                'action' => "Grant/Revoke Entity to Profile",
                'route' => 'profile.toggleEntity',
                'description' => 'Grant/Revoke Entity to Profile',                
            ],    
            
            // FEB Guillon Familias
            [
                'entity_id' => '8',                 // Entity foreign key
                'action' => 'Famílias Listar',
                'route' => 'familia.index',
                'description' => 'Famílias Listar',
            ],
            [
                'entity_id' => '8',                 // Entity foreign key
                'action' => 'Famílias Ver',
                'route' => 'familia.show',
                'description' => 'Famílias Ver',
            ],
            [
                'entity_id' => '8',                 // Entity foreign key    
                'action' => 'Famílias Inserir',
                'route' => 'familia.store',
                'description' => 'Famílias Inserir',
            ],
            [
                'entity_id' => '8',                 // Entity foreign key    
                'action' => 'Famílias Atualizar',
                'route' => 'familia.update',
                'description' => 'Famílias Atualizar',
            ],
            [
                'entity_id' => '8',                 // Entity foreign key    
                'action' => 'Famílias Excluir',
                'route' => 'familia.destroy',
                'description' => 'Famílias Excluir',
            ],               
            
            // FEB Guillon Pessoas
            [
                'entity_id' => '9',                 // Entity foreign key
                'action' => 'Pessoas Listar',
                'route' => 'pessoa.index',
                'description' => 'Pessoas Listar',
            ],
            [
                'entity_id' => '9',                 // Entity foreign key
                'action' => 'Pessoas Ver',
                'route' => 'pessoa.show',
                'description' => 'Pessoas Ver',
            ],
            [
                'entity_id' => '9',                 // Entity foreign key    
                'action' => 'Pessoas Inserir',
                'route' => 'pessoa.store',
                'description' => 'Pessoas Inserir',
            ],
            [
                'entity_id' => '9',                 // Entity foreign key    
                'action' => 'Pessoas Atualizar',
                'route' => 'pessoa.update',
                'description' => 'Pessoas Atualizar',
            ],
            [
                'entity_id' => '9',                 // Entity foreign key    
                'action' => 'Pessoas Excluir',
                'route' => 'pessoa.destroy',
                'description' => 'Pessoas Excluir',
            ],               
            
            // FEB Guillon Pessoas
            [
                'entity_id' => '10',                 // Entity foreign key
                'action' => 'Pedidos Listar',
                'route' => 'pedido.index',
                'description' => 'Pedidos Listar',
            ],
            [
                'entity_id' => '10',                 // Entity foreign key
                'action' => 'Pedidos Ver',
                'route' => 'pedido.show',
                'description' => 'Pedidos Ver',
            ],
            [
                'entity_id' => '10',                 // Entity foreign key    
                'action' => 'Pedidos Inserir',
                'route' => 'pedido.store',
                'description' => 'Pedidos Inserir',
            ],
            [
                'entity_id' => '10',                 // Entity foreign key    
                'action' => 'Pedidos Atualizar',
                'route' => 'pedido.update',
                'description' => 'Pedidos Atualizar',
            ],
            [
                'entity_id' => '10',                 // Entity foreign key    
                'action' => 'Pedidos Excluir',
                'route' => 'pedido.destroy',
                'description' => 'Pedidos Excluir',
            ],               
            
            // FEB Eventos Veículos
            [
                'entity_id' => '11',                 // Entity foreign key
                'action' => 'Veículos Listar',
                'route' => 'veiculo.index',
                'description' => 'Veículos Listar',
            ],
            [
                'entity_id' => '11',                 // Entity foreign key
                'action' => 'Veículos Ver',
                'route' => 'veiculo.show',
                'description' => 'Veículos Ver',
            ],
            [
                'entity_id' => '11',                 // Entity foreign key    
                'action' => 'Veículos Inserir',
                'route' => 'veiculo.store',
                'description' => 'Veículos Inserir',
            ],
            [
                'entity_id' => '11',                 // Entity foreign key    
                'action' => 'Veículos Atualizar',
                'route' => 'veiculo.update',
                'description' => 'Veículos Atualizar',
            ],
            [
                'entity_id' => '11',                 // Entity foreign key    
                'action' => 'Veículos Excluir',
                'route' => 'veiculo.destroy',
                'description' => 'Veículos Excluir',
            ],               
            
            // FEB Eventos Rotas
            [
                'entity_id' => '12',                 // Entity foreign key
                'action' => 'Rotas Listar',
                'route' => 'rota.index',
                'description' => 'Rotas Listar',
            ],
            [
                'entity_id' => '12',                 // Entity foreign key
                'action' => 'Rotas Ver',
                'route' => 'rota.show',
                'description' => 'Rotas Ver',
            ],
            [
                'entity_id' => '12',                 // Entity foreign key    
                'action' => 'Rotas Inserir',
                'route' => 'rota.store',
                'description' => 'Rotas Inserir',
            ],
            [
                'entity_id' => '12',                 // Entity foreign key    
                'action' => 'Rotas Atualizar',
                'route' => 'rota.update',
                'description' => 'Rotas Atualizar',
            ],
            [
                'entity_id' => '12',                 // Entity foreign key    
                'action' => 'Rotas Excluir',
                'route' => 'rota.destroy',
                'description' => 'Rotas Excluir',
            ],               
            
            // FEB Eventos Viagens
            [
                'entity_id' => '13',                 // Entity foreign key
                'action' => 'Viagens Listar',
                'route' => 'viagen.index',
                'description' => 'Viagens Listar',
            ],
            [
                'entity_id' => '13',                 // Entity foreign key
                'action' => 'Viagens Ver',
                'route' => 'viagen.show',
                'description' => 'Viagens Ver',
            ],
            [
                'entity_id' => '13',                 // Entity foreign key    
                'action' => 'Viagens Inserir',
                'route' => 'viagen.store',
                'description' => 'Viagens Inserir',
            ],
            [
                'entity_id' => '13',                 // Entity foreign key    
                'action' => 'Viagens Atualizar',
                'route' => 'viagen.update',
                'description' => 'Viagens Atualizar',
            ],
            [
                'entity_id' => '13',                 // Entity foreign key    
                'action' => 'Viagens Excluir',
                'route' => 'viagen.destroy',
                'description' => 'Viagens Excluir',
            ],               
            
        ]);
    }
}