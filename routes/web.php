<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('home', [HomeController::class, 'index'])->name('home');
// Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get ('home', [DashboardController::class, 'index'])->name('home');
Route::get ('listLogins', [DashboardController::class, 'listLogins'])->name('listLogins');
Route::post('logoutUser', [DashboardController::class, 'logoutUser'])->name('logoutUser');

Route::middleware(['auth', 'AccessControlList'])->group(function () {

    Route::controller(OrganizationController::class)->group(function () {
        Route::get( 'organization',           'index')->name('organization.index');
        Route::get( 'organization/show/{id?}', 'show')->name('organization.show');
        Route::post('organization/store',     'store')->name('organization.store');       
        Route::post('organization/update',   'update')->name('organization.update');      
        Route::post('organization/destroy', 'destroy')->name('organization.destroy');
    });              

    Route::controller(SystemController::class)->group(function () {
        Route::get( 'system',           'index')->name('system.index');
        Route::get( 'system/show/{id?}', 'show')->name('system.show');
        Route::post('system/store',     'store')->name('system.store');        
        Route::post('system/update',   'update')->name('system.update');      
        Route::post('system/destroy', 'destroy')->name('system.destroy');
    });              
    
    Route::controller(EntityController::class)->group(function () {
        Route::get('entity/listActions/{id?}','listActions')->name('entity.listActions');
        Route::get('entity/listAuthorzs',     'listAuthorzs')->name('entity.listAuthorzs');
    
        Route::get( 'entity',                    'index')->name('entity.index');
        Route::get( 'entity/show/{id?}',          'show')->name('entity.show');
        Route::post('entity/store',              'store')->name('entity.store');        
        Route::post('entity/update',            'update')->name('entity.update');      
        Route::post('entity/destroy',          'destroy')->name('entity.destroy');
    
        Route::post('entity/listEntities','listEntities')->name('entity.listEntities');
    });              
    
    Route::controller(ProfileController::class)->group(function () {
        Route::get( 'profile',                      'index')->name('profile.index');
        Route::get( 'profile/show/{id?}',            'show')->name('profile.show');
        Route::post('profile/store',                'store')->name('profile.store');        
        Route::post('profile/update',              'update')->name('profile.update');      
        Route::post('profile/destroy',            'destroy')->name('profile.destroy');
       
        Route::post('profile/grantEntity',    'grantEntity')->name('profile.grantEntity');
        Route::post('profile/toggleSystem',  'toggleSystem')->name('profile.toggleSystem');    
        Route::post('profile/listSystems',    'listSystems')->name('profile.listSystems');    

        Route::post('profile/toggleEntity',  'toggleEntity')->name('profile.toggleEntity');
    });              
    
    Route::controller(MenuController::class)->group(function () {
        Route::get( 'menu',                      'index')->name('menu.index');
        Route::get( 'menuadmin',                 'admin')->name('menu.admin');
        Route::get( 'menu/listDados',        'listDados')->name('menu.listDados');
        Route::get( 'menu/show/{id?}',            'show')->name('menu.show');
        Route::post('menu/store',                'store')->name('menu.store');        
        Route::post('menu/destroy/{id?}',      'destroy')->name('menu.destroy');
        Route::put('menu/update/{id?}',         'update')->name('menu.update');
       
        Route::get('menu/listRoleMenus/{id?}','listRoleMenus')->name('menu.listRoleMenus');
        Route::post('menu/saveRoleMenus/{id?}','saveRoleMenus')->name('menu.listRoleMenus');
        Route::delete('menu/removeMenuFromRole/{id?}','removeMenuFromRole')->name('menu.removeMenuFromRole');
    });              
    
    Route::controller(ActionController::class)->group(function () {
        Route::post('profile/grantEntity',    'grantEntity')->name('profile.grantEntity');
    
        // after common routes
        Route::get( 'action',           'index')->name('action.index');
        Route::get( 'action/show/{id?}', 'show')->name('action.show');
        Route::post('action/store',     'store')->name('action.store');        
        Route::post('action/update',   'update')->name('action.update');      
        Route::post('action/destroy', 'destroy')->name('action.destroy');    
    });              
    
    Route::controller(UserController::class)->group(function () {
    
        // Route::post('user/resetPassword','resetPassword')->name('user.resetPassword');
        Route::get( 'user/refreshDB',          'refreshDB')->name('user.refreshDB');

        Route::post('user/listRoles',          'listRoles')->name('user.listRoles');
        Route::post('user/listSystems',      'listSystems')->name('user.listSystems');
        Route::post('user/listOrganiz',      'listOrganiz')->name('user.listOrganiz');
        Route::get( 'user/listActiveAuth','listActiveAuth')->name('user.listActiveAuth');
        
        Route::get( 'user',                  'index')->name('user.index');
        Route::get( 'user/show/{id?}',        'show')->name('user.show')->where('id','[0-9]+');
        Route::post('user/store',            'store')->name('user.store');        
        Route::post('user/update',          'update')->name('user.update'); 
        // Foi para API > Route::post('user/updatephoto', 'updatePhoto')->name('user.updatephoto'); 
        
        Route::post('user/destroy',       'destroy')->name('user.destroy');

        Route::post('user/updateProfile/{id?}', 'updateProfile')->name('user.updateProfile')->where('id','[0-9]+');
    
        Route::post('user/toggleRole',      'toggleRole')->name('user.toggleRole');
        Route::post('user/toggleSystem',  'toggleSystem')->name('user.toggleSystem');
        Route::post('user/toggleAuthorz','toggleAuthorz')->name('user.toggleAuthorz');
        Route::post('user/toggleOrganiz','toggleOrganiz')->name('user.toggleOrganiz');
    });    
});              

// Route::controller(SystemController::class)->group(function () {
//     Route::get( 'system',          'index')->name('system.index');
//     Route::get( 'system/{id?}',     'show')->name('system.show');
//     Route::post('system/store',    'store')->name('system.store');        // insert no registro
//     Route::post('system/update',  'update')->name('system.update');      // update no registro        
//     Route::post('system/destroy','destroy')->name('system.destroy');
// });              

// Route::controller(EntityController::class)->group(function () {
//     // Route::post('entity/list',         'list')->name('entity.list');
//     Route::get('entity/listActions/{id?}','listActions')->name('entity.listActions');
//     Route::get('entity/listAuthorzs',     'listAuthorzs')->name('entity.listAuthorzs');

//     Route::get( 'entity',                    'index')->name('entity.index');
//     Route::get( 'entity/{id?}',               'show')->name('entity.show');
//     Route::post('entity/store',              'store')->name('entity.store');        
//     Route::post('entity/update',            'update')->name('entity.update');      
//     Route::post('entity/destroy',          'destroy')->name('entity.destroy');

//     Route::post('entity/listEntities','listEntities')->name('entity.listEntities');
//     Route::post('entity/toggleEntity','toggleEntity')->name('entity.toggleEntity');
// });              

// Route::controller(ProfileController::class)->group(function () {
//     Route::get( 'profile',                      'index')->name('profile.index');
//     Route::get( 'profile/{id?}',                 'show')->name('profile.show');
//     Route::post('profile/store',                'store')->name('profile.store');        
//     Route::post('profile/update',              'update')->name('profile.update');      
//     Route::post('profile/destroy',            'destroy')->name('profile.destroy');
   
//     Route::post('profile/grantEntity',    'grantEntity')->name('profile.grantEntity');
//     Route::post('profile/toggleSystem',  'toggleSystem')->name('profile.toggleSystem');    
//     Route::post('profile/listSystems',    'listSystems')->name('profile.listSystems');    
// });              

// Route::controller(ActionController::class)->group(function () {
//     // first especific routes
//     Route::post('profile/grantEntity',    'grantEntity')->name('profile.grantEntity');

//     // after common routes
//     Route::get( 'action',          'index')->name('action.index');
//     Route::get( 'action/{id?}',     'show')->name('action.show');
//     Route::post('action/store',    'store')->name('action.store');        
//     Route::post('action/update',  'update')->name('action.update');      
//     Route::post('action/destroy','destroy')->name('action.destroy');    
// });              

// Route::controller(UserController::class)->group(function () {

//     // Route::get('user/migrateFreshSeed','migrateFreshSeed')->name('user.migrateFreshSeed');

//     // Route::post('user/resetPassword','resetPassword')->name('user.resetPassword');
//     Route::post('user/listRoles',        'listRoles')->name('user.listRoles');
//     Route::post('user/listSystems',    'listSystems')->name('user.listSystems');
//     Route::post('user/listOrganiz',    'listOrganiz')->name('user.listOrganiz');
//     Route::get('user/listActiveAuth', 'listActiveAuth')->name('user.listActiveAuth');

//     Route::get( 'user',          'index')->name('user.index');
//     Route::get( 'user/{id?}',     'show')->name('user.show');
//     Route::post('user/store',    'store')->name('user.store');        
//     Route::post('user/update',  'update')->name('user.update');      
//     Route::post('user/destroy','destroy')->name('user.destroy');


//     Route::post('user/toggleRole',      'toggleRole')->name('user.toggleRole');
//     Route::post('user/toggleSystem',  'toggleSystem')->name('user.toggleSystem');
//     Route::post('user/toggleAuthorz','toggleAuthorz')->name('user.toggleAuthorz');
//     Route::post('user/toggleOrganiz','toggleOrganiz')->name('user.toggleOrganiz');
// });

Route::get('/admin', function () {
    phpinfo();
});

Route::get('/admin/migrateFreshSeed', function () {
    // if (auth()->check()) { // Verifique se o usuário é um administrador

    /**
     * Caso algo dê errado na operação abaixo
     * O site não permitirá mais login
     * ou irá mostrar um erro no AuthServiceProvider
     * 
     * Para superar isso há que
     * Rodar os comando migrates e seeders dentro do containter do app
     * 
     *  Esteja na pasta do app
     *  no terminal entre no container com: docker exec -it acl4-php bash
     * 
     *  1 - php artisan
     *  2 - php artisan db:seed --force
     *  
     * 
     */

    if (1 == 1) { // Verifique se o usuário é um administrador

        $output = '';

        // $linkToImages = Artisan::call('php artisan storage:link');
 
        $exitCode = Artisan::call('migrate:fresh --force');
        $output .= Artisan::output();                   // Capturando a saída

        // Se o migrate:fresh foi bem-sucedido, rodar o seeder
        if ($exitCode === 0) {
            // Rodando o seeder para popular o banco de dados
            $exitCode = Artisan::call('db:seed --force');
            $output .= "<br/><br/>\n" . Artisan::output();  // Capturando a saída do seeder
        }

        // Verificar se o comando foi bem-sucedido (o código de saída 0 significa sucesso)
        if ($exitCode === 0) {
            return redirect()->back()->with('status', "Migração executada com sucesso! <br/><br/>\n" . $output);
        } else {
            return redirect()->back()->with('error', 'Falha ao executar a migração.' . $output);
        }

    } else {
        return redirect()->back()->with('error', 'Você não tem permissão para rodar migrações.');
    }
});
