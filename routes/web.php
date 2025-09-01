<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;

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
    
    Route::controller(ActionController::class)->group(function () {
        // first especific routes
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

Route::get('/admin/fixStorageLink', function () {

    // Verifica se está em produção e bloqueia se for o caso
    // if (app()->environment('production')) {
    //     abort(403, 'Esta ação não é permitida em ambiente de produção.');
    // }
    
    // Verifica se o usuário está autenticado e é administrador
    // if (!auth()->check() || !auth()->user()->is_admin) {
    //     abort(403, 'Acesso não autorizado.');
    // }    
        
    $results = [];
    
    try {
        // 1. Recria o link simbólico
        Artisan::call('storage:link');
        $results['storage_link'] = Artisan::output();
        
        // 2. Limpa caches antigos
        Artisan::call('optimize:clear');
        $results['optimize_clear'] = Artisan::output();
        
        // 3. Otimiza a aplicação
        Artisan::call('optimize');
        $results['optimize'] = Artisan::output();
        
        // 4. Cache de configuração
        Artisan::call('config:cache');
        $results['config_cache'] = Artisan::output();
        
        // 5. Cache de rotas
        Artisan::call('route:cache');
        $results['route_cache'] = Artisan::output();
        
        // 6. Cache de views
        Artisan::call('view:cache');
        $results['view_cache'] = Artisan::output();
        
        // // 7. Ajustar permissões (executa comandos shell)
        // $storagePerms = shell_exec('chmod -R 755 storage/ 2>&1');
        // $publicPerms = shell_exec('chmod -R 755 public/ 2>&1');
        
        // $results['permissions'] = [
        //     'storage' => $storagePerms,
        //     'public' => $publicPerms
        // ];
        
        return response()->json([
            'success' => true,
            'message' => 'Scripts de deploy executados com sucesso!',
            'results' => $results
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro: ' . $e->getMessage(),
            'results' => $results
        ], 500);
    }         

});

