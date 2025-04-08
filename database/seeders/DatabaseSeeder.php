<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        /**
         * 
         * 
         * php artisan make:model ModelName -mcrs. Are not request implentation on Laavel
         * -m: Cria a migration (tabela associada ao modelo).
         * -c: Cria o controller.
         * -r: Cria o resource (para API, por exemplo).
         * -s: Cria o seeder.
         * 
         * php artisan make:request ProductRequest
         * 
         */

        /**
         * Note: to run follow this steps
         * 1 - Access the terminal
         * 2 - Erase all databases tables: php artisan migrate:fresh
         * 3 - Execute all the seeders: php artisan db:seed
         */

        // disable all FOREIGN_KEY
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');  

            // Truncate tables before run de seeders
            DB::table('users')->truncate();

            DB::table('acl_systems')->truncate();
            DB::table('acl_authorizations')->truncate();
            DB::table('acl_actions')->truncate();
            DB::table('acl_entities')->truncate();
            DB::table('acl_profiles')->truncate();
            DB::table('acl_organizations')->truncate();

        // enable all FOREIGN_KEY
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');  // Reabilita as restrições de chave estrangeira        


        // Call FEB Seeders
        $this->call([
            FEB_OrganizationsSeeder::class,
            FEB_UsersSeeder::class,
            FEB_SystemsSeeder::class,
            FEB_EntitiesSeeder::class,
            FEB_ActionsSeeder::class,
            FEB_ProfilesSeeder::class,
            FEB_SystemsUsersSeeder::class,
            FEB_AuthorizationsSeeder::class,
            FEB_OrganizationsUsersSeeder::class,
            FEB_ProfilesUsersSeeder::class,
            FEB_VeiculosSeeder::class,
        ]);  

        // Call Default Seeders
        // $this->call([
        //     ACL_OrganizationsSeeder::class,
        //     ACL_UsersSeeder::class,

        //     ACL_SystemsSeeder::class,
        //     ACL_EntitiesSeeder::class,
        //     ACL_ActionsSeeder::class,       // Rota
        //     ACL_ProfilesSeeder::class,
        //     ACL_ProfilesUsersSeeder::class,
        //     ACL_AuthorizationsSeeder::class,
        //     ACL_SystemsUsersSeeder::class,
        //     ACL_OrganizationsUsersSeeder::class, 
        // ]);  
        
    }
}
