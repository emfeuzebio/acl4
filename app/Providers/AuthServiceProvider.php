<?php

namespace App\Providers;

use App\Models\Action;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //

        $this->registerPolicies();  // mandatory

        /**
         * NOTA:    Auth::User() não está disponível aqui. O User ainda não foi autentidado
         *          Não é recomendado recuperar o Auth::User() aqui no AuthServiceProvider
         *          por isso, abaixo pegamos todas as Autorizações Ativas e depois validamos 
         *          se o User Atual têm acesso à elas conforme seus Perfis
         */


                    // $user = Auth::user();                // Isso não funciona porque aqui o Auth ainda não tem o User
                    $user = User::first();                  // Então pegamos o corrente User
                    $abilities = $user->grantedActions();     // load the active authorizations for all Users
                    // dd($abilities);   

                    // iterates through all active abilities for all Users, i.e. through all active authorizations
                    // and create a Gate for each authorizations with the name like route only to User Logged
                    foreach ($abilities as $route) {
                        Gate::define($route, function (User $user) use ($route) {
                            return in_array($route, $user->grantedActions());     // autoriza apenas os os User Logado
                            // return true;                                     // autoriza todos
                        });
                    }
                    
                    // EUZ ver as abilities carregadas
                    $abilities = Gate::abilities();
                    // dd($abilities);



            /**
             * Estudos
             */
            // Criar Gates individuais para cada rota permitida do usuário
            // Gate::define('user.profileA', function (User $user) {
            //     return true;
            // });
            // Gate::define('user.profileB', function (User $user) {
            //     return true;
            // });
            // Gate::define('user.profileC', function (User $user) {
            //     return true;
            // });
            // $abilities = ['aa','bb','cc'];
            // foreach ($abilities as $route) {
            //     Gate::define($route, function (User $user) {
            //         return true;
            //     });
            // }
            // $permissoes = Gate::abilities();
            // dd($permissoes);   

 
            /**
             * Estudos
             * FUNCIONANDO sem Gate para cada Action            
             */            
            // Gate::define('access-route', function (User $user) {
            //     $route = request()->route() ? request()->route()->getName() : null;
            //     if (! $route) {
            //         return false; // Se não houver rota, nega o acesso
            //     }
            //     // print_r($route);
            //     // dd($user->grantedActions());
            //     return in_array($route, $user->grantedActions());
            // });             

    }
}
