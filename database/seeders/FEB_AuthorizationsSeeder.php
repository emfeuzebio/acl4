<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_AuthorizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Let's get all rows from the 'acl_actions' table
        $actions = DB::table('acl_actions')->get();

        /**
         * ADMINISTRATOR Profile
         * Authorization's Administrator User ID 1 on Profile_id = 1
         * All Authorizations active Yes
         */
        $profile_id = 1;    // Administrator  

        // Lets's iterate through each row and add a record to the 'acl_authorizations' table
        foreach ($actions as $action) {
            DB::table('acl_authorizations')->insert([
                'profile_id' => $profile_id, 
                'action_id'  => $action->id,            // primary key of acl_actions
                'active'     => 'Y',                    // authorized default 'Y'es
                'created_at' => now(), 
                'updated_at' => now(), 
            ]);
        }


        /**
         * MANAGER Profile
         * Authorization's Manager User ID 2 on Profile_id = 2
         * All Authorizations active Yes
         */
        $profile_id = 2;    // Gerente de Transportes
        $actionsAuthorized = ['index','show','updateProfile','listActions','listEntities','listAuthorzs','listRoles','listSystems', 'listActiveAuth', 'listOrganiz'];
        // dd($actions);

        // Lets's iterate through each row and add a record to the 'acl_authorizations' table
        foreach ($actions as $action) {

            // somente as Actions específicas para Ger Transp
            // if (! in_array($action->entity_id,[1,2,3,4,5,6,7, 11,12,13])) {
            if (! in_array($action->entity_id,[5,6,7, 11,12,13])) {
                continue;
            }
            
            $route = $action->route;

            $active = array_filter($actionsAuthorized, function ($action) use ($route) {
                return str_contains($route, $action);   // Verifica se $route contém $action
            });
            
            $active = ! empty($active) ? 'Y' : 'N';

            DB::table('acl_authorizations')->insert([
                'profile_id' => $profile_id, 
                'action_id'  => $action->id,            // primary key of acl_actions
                'active'     => $active,                // authorized default 'N'o
                'created_at' => now(), 
                'updated_at' => now(), 
            ]);
        }

        /**
         * USER Profile
         * Authorization's User User ID 3 on Profile_id = 3
         * All Authorizations active Yes
         */
        $profile_id = 3;    // USER Profile
        $actionsAuthorized = ['index','show','updateProfile','listActions','listEntities','listAuthorzs','listRoles','listSystems', 'listActiveAuth', 'listOrganiz'];        

        // Lets's iterate through each row and add a record to the 'acl_authorizations' table
        foreach ($actions as $action) {

            // somente as Actions específicas para Ger Doações
            if (! in_array($action->entity_id,[5,6,7, 8,9,10])) {
            // if ($action->entity_id > 9) {
                continue;
            }

            $route = $action->route;

            $active = array_filter($actionsAuthorized, function ($action) use ($route) {
                return str_contains($route, $action);   // Verifica se $route contém $action
            });
            
            $active = ! empty($active) ? 'Y' : 'N';

            DB::table('acl_authorizations')->insert([
                'profile_id' => $profile_id, 
                'action_id'  => $action->id,            // primary key of acl_actions
                'active'     => $active,                // authorized default 'N'o
                'created_at' => now(), 
                'updated_at' => now(), 
            ]);
        }
 

    }
}
