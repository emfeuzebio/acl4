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
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    use ACLTrait;
    
    public function __construct() 
    {
        $this->middleware('auth');
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

    public function admin(Request $request) 
    {
        $user = Auth::user();
        $userId = $user->id;

        // vamos recuperar a lista de Systems concedidos ao User
        $systems = System::where('active', 'Y')
            ->whereHas('users', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('name')
            ->get();

        return view('acl/MenuAdmin2', compact('systems'));
    }

    public function listDados(Request $request) 
    {
        // Todos os dados abaixo são filtrados pelo System
        $user = Auth::user();
        $userId = $user->id;

        $systems = System::where('active', 'Y')
            ->whereHas('users', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->orderBy('name')->get();

        $menus = Menu::with('children')->where('system_id',$request->systemId)->whereNull('menu_id')->orderBy('position')->get();

        $menusPai = Menu::where('system_id',$request->systemId)->whereNull('menu_id')->orderBy('position')->get();

        $profiles = Profile::where('system_id',$request->systemId)->where('active','Y')->OrderBy('name')->get();

        /* 
            Recupera todas as Rotas do SystemId

            SELECT acl_actions.id, acl_actions.route 
            FROM acl_profiles
                INNER JOIN acl_authorizations ON acl_authorizations.profile_id = acl_profiles.id
                INNER JOIN acl_actions ON acl_actions.id = acl_authorizations.action_id
            WHERE acl_profiles.system_id = 1
            ORDER BY acl_actions.entity_id, acl_actions.id;
        */            
        $routes = Profile::where('system_id', $request->systemId)->with('actions')->get()
            ->pluck('actions')->flatten()->sortBy(['entity_id', 'id'])->values();

        return response()->json([
            'systems' => $systems,
            'profiles' => $profiles,
            'routes' => $routes,
            'menusPai' => $menusPai,
            'menus' => $menus,
        ]);
    }

    public function listRoleMenus($roleId, $html = false )
    {
        if (!$roleId) {
            return [];
        }

        // recupera os Itens de Menu do Perfil ordenados pelo position
        $role = Profile::with(['menus' => function($query) {
            $query->orderByPivot('position', 'asc');
        }])->findOrFail($roleId);        

        if ($html) {
            $html = view('acl/MenuAdminProfiles', ['menus' => $role->menus])->render();
            return response()->json(['html' => $html]);
        } else {
            return response()->json($role->menus);
        }
    }    

    public function saveMenusOrder(Request $request, $systemId)
    {
        try {

            $request->validate([
                'menus' => 'required|array',
                'menus.*.id' => 'required|integer|exists:acl_menus,id',
                'menus.*.position' => 'required|integer|min:1'
            ]);
            
            $systemId = (int) $systemId;
            
            foreach ($request->menus as $menuData) {
                Menu::where('id', $menuData['id'])
                    ->where('system_id', $systemId)
                    ->update(['position' => $menuData['position']]);
            }            

            return response()->json([
                'success' => true,
                'message' => 'Novas posições dos Itens de Menu salvas com sucesso.'
            ], Response::HTTP_OK);
            
        } catch (Exception $e) {        

            return response()->json([
                'sucesso' => false,
                'error' => 'Não foi possível atualizar as posições dos Itens de Menu. ' . $e->getCode() . '-' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);            
        }
    }    

    public function saveRoleMenus(Request $request, $roleId)
    {
        try {
            if (empty($request->menus)) {
                throw new Exception("<b>NÃO HÁ itens de Menu para salvar.</b>");
            }

            $role = Profile::findOrFail($roleId);

            // Faz sync salvando as novas posições - Apaga todos os registro Pivot e insere novamente
            $role->menus()->sync($request->menus);

            return response()->json([
                'success' => true,
                'message' => 'Novas posições dos Itens de Menu salvas com sucesso.'
            ], Response::HTTP_OK);
            
        } catch (Exception $e) {        

            return response()->json([
                'sucesso' => false,
                'message' => 'Não foi possível atualizar as posições dos Itens de Menu. ' . $e->getMessage(),
                'error' => 'Não foi possível atualizar as posições dos Itens de Menu. ' . $e->getCode() . '-' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);            
        }
    }    

    public function saveMenuActive(Request $request, $menuId)
    {
        try {
            // $request->active = '';
            // $menuId = 999;

            if (empty($request->active)) {
                throw new Exception("<b>Item de Menu não informado.</b>");
            }

            $menu = Menu::findOrFail($menuId);
            $menu->active = $request->active;
            $menu->save();            

            return response()->json([
                'success' => true,
                'message' => "Itens de Menu $menuId " . ($request->active == 'Y' ? 'ATIVADO' : 'DESATIVADO') . " com sucesso.",
            ], Response::HTTP_OK);
            
        } catch (Exception $e) {        

            return response()->json([
                'sucesso' => false,
                'message' => 'Erro: ' . $e->getMessage(),
                'error' => 'Erro:. ' . $e->getCode() . '-' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);            
        }
    }    

    public function removeMenuFromRole(Request $request, $profileId)
    {
        try {
            $profile = Profile::findOrFail($profileId);

            $profile->menus()->detach($request->menuId);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu Removido do Perfil de Acesso com sucesso.',
            ], Response::HTTP_OK);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao Remover o Item de Menu do Perfil de Acesso. ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    


    public function show(Request $request)
    {        
        return;
    }      

    public function store(Request $request)
    {
        try {
            $request->validate([
                'system_id' => 'required|exists:acl_systems,id',
                'menu_id' => 'nullable|exists:acl_menus,id',
                'name' => 'required|string|max:255',
                'icon' => 'nullable|string|max:50',
                'route' => 'required|string|max:255',
                'position' => 'integer',
                'active' => 'in:Y,N'
            ]);

            $menu = Menu::Create(
                $request->only(['system_id','menu_id', 'name', 'icon', 'route', 'position', 'active'])
            );  

            // return response()->json(['success' => true, 'message' => 'Operação realizada com sucesso.', 'dados' => $menu], Response::HTTP_OK);
            return response()->json(['success' => true, 'message' => "Menu <b>'{$menu->name}'</b> Inserido com sucesso.", 'dados' => $menu], Response::HTTP_OK);
                        
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

            return response()->json(['success' => true, 'message' => "Menu '{$menu->name}' Atualizado com sucesso.", 'dados' => $menu], Response::HTTP_OK);
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
                $menu->delete();    // delete Cascade on menu_profile
                return response()->json(['success' => true, 'message' => "Menu <b>'{$menu->name}'</b> Excluído com sucesso.", 'dados' => $menu], Response::HTTP_OK);
            } else {
                return Response()->json(['message' => "Exception: Não é possível Excluir o Menu <b>'{$menu->name}'</b>, pois está concedido a Outros Perfis de Acesso"], Response::HTTP_UNPROCESSABLE_ENTITY); //422
            }
            
        } catch (Exception $e) {        

            return response()->json([
                'sucesso' => false,
                'message' => ($e->getCode() == 23000 ? 'Impossível EXCLUIR porque há registros relacionados. (SQL-1451).' : 'Houve um ERRO desconhecido! A Operação foi cancelada.'),
                'error' => ($e->getCode() == 23000 ? 'errorMessage1451' : $e->getMessage()),
                'code' => $e->getCode(),
            ], Response::HTTP_FORBIDDEN);            
        }

        return response()->json(['success' => true, 'message' => 'Operação realizada com sucesso.'], Response::HTTP_OK);
    }   

}
