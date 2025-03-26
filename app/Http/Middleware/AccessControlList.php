<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/*

    chatgpt prompt

    Laravel 10 Eloquent

    Tenho um sistema de Access Control List (ACL) com a estrututa

    Uma Entity pode ter várias Actions que tem as colunas 'model' e 'route'
    Uma Action pode ter várias Authorizations
    Authorizations é a tabela pivot entre Profile e Action e tem a coluna active = 'Y'
    Um User pode ter vários Profiles
    Profile_User é a tabela pivot entre Profile e User

    No final tenho a Access Control List do User logado a partir da coluna 'route' da abelas Authorizations retirando as eventuais Actions repetidas.

    Por fim, preciso de sua solução para:

    Criar um Middleware chamdo AccessControlList para controlar todos acessos via 'route' ao sistema baseado em Gates.
    Ao fazer login quero obter Authorizations do User, listar todas Authorizations e controlar o acesso via Middleware AccessControlList 

*/

class AccessControlList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    use AuthorizesRequests;

    public function handle(Request $request, Closure $next): Response
    {
        /**
         * ACL 2
         */
        try {

            // $abilities = Gate::abilities();
            // $abilityNames = array_keys($abilities);
            // return response()->json($abilityNames);
            // die('abilities');

            $currentRoute = $request->route()->getName();
            $this->authorize($currentRoute);                // Verifica se há um Gate definido para essa rota

        } catch (Exception $e) {

            // if ajax request  
            if (request()->ajax()) {
                return response()->json(['message' => 'Usuário NÃO Autorizado na rota: <b>' . $currentRoute . '</b>'], Response::HTTP_FORBIDDEN);
            } 

            // else if is the full-page - default request HTTP redirect to Home with error message
            return redirect()->route('home')->with('error', 'Acesso Negado à rota: ' . $currentRoute);
        }

        return $next($request);


                        // /**
                        //  * ACL 2
                        //  */
                        // $user = Auth::user();
                        // // dd($user->load('profiles.authorizations.action'));
                        // // dd($user);

                        // if (! $user) {
                        //     return response()->json(['message' => 'noauthenticated'], Response::HTTP_FORBIDDEN);
                        // }

                        // // $abilities = Gate::abilities();
                        // // dd($abilities);
                        // // tem 46 abilities


                        // $currentRoute = $request->route()->getName();
                        // $allowedRoutes = $user->getAclRoutes();
                        // // dd($allowedRoutes);

                        // if (! in_array($currentRoute, $allowedRoutes)) {

                        //     // if ajax request
                        //     if (request()->ajax()) {
                        //         return response()->json(['message' => 'Usuário NÃO Autorizado na rota: <b>' . $currentRoute . '</b>'], Response::HTTP_FORBIDDEN);
                        //     } 

                        //     // else is the full-page - default request HTTP
                        //     return redirect()->route('home')->with('error', 'Acesso Negado à rota: ' . $currentRoute);
                        // }        

                        // return $next($request);     


            // // Verifica se o usuário tem permissão para a rota
            // if (! Gate::allows($currentRoute)) {

            //     if (request()->ajax()) {
            //         return response()->json(['message' => 'Usuário NÃO Autorizado na rota: <b>' . $currentRoute . '</b>'], Response::HTTP_FORBIDDEN);
            //     }

            //     // Redireciona para a home com a mensagem "Acesso Negado"
            //     return redirect()->route('home')->with('error', 'Acesso Negado à rota: ' . $currentRoute);
            // }

            // return $next($request);        


        // $allowedRoutes = $user->getAclRoutes();
        // // dd($allowedRoutes);

        // $currentRoute = $request->route()->getName();
        // // echo "currentRoute: $currentRoute";
        // // dd($currentRoute);

        //     if (! in_array($currentRoute, $allowedRoutes)) {
        //         // abort(403, 'Acesso negado.');
        //         // abort(Response::HTTP_FORBIDDEN, 'Acesso negado.');
        //         // return redirect()->route('home')->with('error', 'Acesso Negado à rota: ' . $currentRoute);
        //         // return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);

        //         if (request()->ajax()) {
        //             // return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        //             // return response()->json(['message' => 'Acesso Negado à rota: ' . $currentRoute], Response::HTTP_FORBIDDEN);
        //             return response()->json(['message' => 'Usuário NÃO Autorizado na rota: <b>' . $currentRoute . '</b>'], Response::HTTP_FORBIDDEN);
        //         } 

        //         return redirect()->route('home')->with('error', 'Acesso Negado à rota: ' . $currentRoute);
        //     }

        // // if (!Gate::allows('access-route', request()->route()->getName())) {
        // //     abort(403, 'Acesso negado.');
        // //     // return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        // // }        

        // // Verificando acesso via Gate
        // // if (!Gate::allows('access-route', $currentRoute)) {
        // //     return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        // // }        

        // return $next($request);     
    }
}
