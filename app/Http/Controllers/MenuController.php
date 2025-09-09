<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\System;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Traits\ACLTrait;
use Exception;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    use ACLTrait;
    
    public function __construct() 
    {
        $this->middleware('auth');
    }

    public function admin(Request $request) 
    {
        return view('acl/MenuAdmin2');
    }



    public function listDados(Request $request) 
    {
        // verifica se recebeu o SystemId no request
        $systemId = $request->input('systemId');
        // if (! $systemId) {
        //     return response()->json(['error' => 'Informar o SystemId é obrigatório.'], Response::HTTP_UNAUTHORIZED);   // 401
        // }

        // TODO filtrar por apenas os Systems que o User tem acesso
        $systems = System::where('active','Y')->orderBy('name')->get();

        // TODO filtrar pelo System filtro
        $menus = Menu::with('children')->whereNull('menu_id')->orderBy('position')->get();

        $menusPai = Menu::whereNull('menu_id')->orderBy('position')->get();

        // TODO filtrar pelo System filtro
        $profiles = Profile::where('active','Y')->OrderBy('name')->get();

        return response()->json([
            'systems' => $systems,
            'profiles' => $profiles,
            'menusPai' => $menusPai,
            'menus' => $menus,
        ]);
    }




    public function index(Request $request) 
    {
        // $autorizacaos = Autorizacao::where('active','=','SIM')->with('perfil')->get();
        // $autorizacaos = Autorizacao::where('active','=','SIM')->with('perfil');
        // dd($autorizacaos->toSql());

        if (request()->ajax()) {
            
            $menus = Menu::with('children')->whereNull('menu_id')->orderBy('position')->get();
            $roles = Profile::where('active', 'Y')->get();

                // if you received the filter page parameters apply the filter in where
                // if ($request->input('filterSelect1')) {
                //     $query->where('system_id', $request->input('filterSelect1'));
                // }            

                // execute the query with filter parameters
                // $profiles = $query->get();

                // get the authorizations granted to the entity 
                // $authorizations = $this->getAbilities('profile');   // using Trait
                // $authorizations = [
                //     'profile.index', 
                //     'profile.show',
                //     'profile.store',
                //     'profile.update',
                //     'profile.destroy',
                //     'profile.toggleEntity',
                // ];

                // return [
                //     'data' => $profiles,
                //     'authorizations' => $authorizations,
                //     'test' => $listTest,
                // ];
        }

        // load entity configuration file from config/acl
            // $fileConfigJson = config_path('acl/menu.json');
            // $entityConfig = file_exists($fileConfigJson) ? json_decode(file_get_contents($fileConfigJson), false) : [];

        // send list options to view: column 'description' is used in <options>

        $menus = Menu::with('children')->whereNull('menu_id')->orderBy('position')->get();
        $roles = Profile::where('active', 'Y')->get();
        $systems = System::where('active','Y')->get()->sortBy('acronym')->map(function($system) {
            $system->description = $system->acronym;    // create description column from 'acronym'
            return $system;
        });
        // dd($menus);
        // dd($roles);
        // dd($systems);

        // return view('acl/ProfileDatatable',['filterOptions1' => $systems, 'systems' => $systems, 'entityConfig' => $entityConfig]);
        return view('acl/MenuAdmin',['filterOptions1' => $systems, 'menus' => $menus, 'roles' => $roles, 'systems' => $systems]);
    }

    public function listRoleMenus($roleId, $html = false )
    {
        $role = Profile::with('menus')->findOrFail($roleId);

        if ($html) {
            $html = view('acl/MenuAdminProfiles', ['menus' => $role->menus])->render();
            return response()->json(['html' => $html]);
        } else {
            return response()->json($role->menus);
        }
    }    

    public function saveRoleMenus(Request $request, $roleId)
    {
        $role = Profile::findOrFail($roleId);
        $role->menus()->sync($request->menus);
        
        // Atualizar posições
        foreach ($request->menus as $position => $menuId) {
            $role->menus()->updateExistingPivot($menuId, ['position' => $position]);
        }
        
        return response()->json(['success' => true]);
    }    

    public function removeMenuFromRole(Request $request, $profileId)
    {
        try {
            $profile = Profile::findOrFail($profileId);

            $profile->menus()->detach($request->menuId);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu removido do perfil com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover menu: ' . $e->getMessage()
            ], 500);
        }
    }    






    public function show(Request $request)
    {        

        // Habilita o log de todas as consultas SQL executadas a partir deste ponto.
        DB::enableQueryLog();
        
            $profile = Profile::with('authorizations.action')->find($request->id);    // recupera o Perfil com suas Autorizacoes e as Rotas associadas
            $organization['ACLupdate'] = Gate::allows('profile.update');     // returns true if the User has permission to Update            
            // $profile['ACLupdate'] = true;     // returns true if the User has permission to Update

        // Retorna um array com todas as consultas SQL executadas.
        DB::getQueryLog();
        // print_r(DB::getQueryLog()); // para ver as queries num array
        // die();

        return response()->json($profile);
    }      

    public function store(Request $request)
    {
        try {
            $request->validate([
                'menu_id' => 'nullable|exists:acl_menus,id',
                'name' => 'required|string|max:255',
                'icon' => 'nullable|string|max:50',
                'route' => 'required|string|max:255',
                'position' => 'integer',
                'active' => 'in:Y,N'
            ]);

            Menu::Create(
                $request->only(['menu_id', 'name', 'icon', 'route', 'position', 'active'])
            );  

            return response()->json(['success' => true, 'message' => 'Operação realizada com sucesso.'], Response::HTTP_OK);
                        
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Dados de entrada inválidos',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Houve um erro ao salvar os dados do usuário. ' . $e->getMessage(),
                'error' => 'Houve um erro ao salvar os dados do usuário. ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }     

    public function update(Request $request)
    {
        // dd($request);
        try {
            $request->validate([
                'menu_id' => 'nullable|exists:acl_menus,id',
                'name' => 'required|string|max:255',
                'icon' => 'nullable|string|max:50',
                'route' => 'required|string|max:255',
                'position' => 'integer',
                'active' => 'in:Y,N'
            ]);

            $menu = Menu::findOrFail($request->id);
            $menu->update($request->only(['menu_id','name', 'icon', 'route', 'active','position']));

            return response()->json(['success' => true, 'message' => 'Operação realizada com sucesso.'], Response::HTTP_OK);
            
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Dados de entrada inválidos',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Houve um erro ao salvar os dados. ' . $e->getMessage(),
                'error' => 'Houve um erro ao salvar os dados. ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }     

    public function destroy(Request $request)
    {
        try {
            $menu = Menu::findOrFail($request->id);

            // Verifica se o menu tem filhos
            if ($menu->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir um Menu que possui Submenus.'
                ], 422);
            }            

            // the canDelete() is on Model
            if ($menu->canDelete()) {
                // delete Cascade on menu_profile
                $menu->delete();
                return response()->json($menu);
            } else {
                return Response()->json(['message'=>'Exception: Não é possível excluir o Item de Menu, pois está concedido a Outros Perfis de Acesso'], Response::HTTP_UNPROCESSABLE_ENTITY); //422
            }
            
        } catch (Exception $e) {        

            // Get all gerenical errors
            return response()->json([
                'sucesso' => false,
                'message' => ($e->getCode() == 23000 ? 'Impossível EXCLUIR porque há registros relacionados. (SQL-1451).' : 'Houve um ERRO desconhecido! A Operação foi cancelada.'),
                'error' => ($e->getCode() == 23000 ? 'errorMessage1451' : 'errorMessage0000' ),
                'code' => $e->getCode(),
            ], Response::HTTP_FORBIDDEN);            
        }

        return response()->json(['success' => true, 'message' => 'Operação realizada com sucesso.'], Response::HTTP_OK);
    }   

}
