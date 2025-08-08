<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActionRequest;
use Illuminate\Http\Request;
use App\Models\Action;
use App\Models\Authorization;
use App\Models\Entity;
use App\Models\Profile;
use Exception;
use Illuminate\Http\Response;
use App\Traits\ACLTrait;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
{
    use ACLTrait;
    
    public function __construct() 
    {
        $this->middleware('auth');
    }

    public function index(Request $request) 
    {

        // somente Admin e EncPes têm permissão
        // if (Gate::none(['is_admin','is_encpes'], new Sistema())) {
        //     abort(403, 'Usuário não autorizado!');
        // }        

        // $autorizacaos = Autorizacao::where('active','=','SIM')->with('perfil')->get();
        // $autorizacaos = Autorizacao::where('active','=','SIM')->with('perfil');
        // dd($autorizacaos->toSql());
        // dd($autorizacaos);

        if (request()->ajax()) {

            // prepare query initial
            // $query = Action::with('entity','system')->where('entity_id', '>=', 1);
            $query = Action::with('entity.system')->where('entity_id', '>=', 1);

            // if you received the entity_id parameter apply the filter in where
            if ($request->input('entity_id')) {
                $query->where('entity_id', $request->input('entity_id'));
            }            

            // execute the query
            $actions = $query->get();

            // get the authorizations granted to the entity 
            $authorizations = $this->getAbilities('action');  
            // $authorizations = [
            //     'action.show',
            //     'action.update',
            //     'action.destroy',
            //     'action.store',
            //     'action.store',
            // ];

            $actions->each(function ($action) use ($authorizations)  {  // Collection
                $action->setAttribute('authorizations', $authorizations);
            });          

            return [
                'data' => $actions,
                'authorizations' => $authorizations,
            ]; 
        }

        // load entity configuration file from config/acl
        $fileConfigJson = config_path('acl/action.json');
        $entityConfig = file_exists($fileConfigJson) ? json_decode(file_get_contents($fileConfigJson), false) : [];

        // send list options to view: column 'description' is used in <options>
        $entities = Entity::all()->sortBy('acronym')->map(function($entitiy) {
            $entitiy->description = $entitiy->model;    // create description column from 'acronym'
            return $entitiy;
        });        

        return view('acl/ActionDatatable',[ 'filterOptions1' => $entities, 'entities' => $entities, 'entityConfig' => $entityConfig]);
    }

    public function show(Request $request)
    {        
        $action = Action::where('id', $request->id)->first();
        // $action['ACLupdate'] = Gate::allows('sistema.update');     // devolve true se o User tem permissão para Update
        $action['ACLupdate'] = true;     // returns true if the User has permission to Update

        return response()->json($action);
    }      

    public function store(ActionRequest $request)
    {
        try {
            DB::transaction(function () use ($request, &$action) {

                // 1. Cria nova Ação
                $action = Action::create(
                    $request->only(['entity_id', 'action', 'route', 'description'])
                );

                // 2. Busca perfis que já possuem alguma autorização para a mesma entidade
                $profiles = DB::select("
                    SELECT DISTINCT acl_authorizations.profile_id AS id
                    FROM acl_authorizations
                    INNER JOIN acl_actions ON acl_actions.id = acl_authorizations.action_id
                    WHERE acl_actions.entity_id = ?
                ", [$request->entity_id]);

                // 3. Cria uma autorização nova para cada perfil, com a nova ação
                foreach ($profiles as $profile) {
                    Authorization::create([
                        'profile_id' => $profile->id,
                        'action_id'  => $action->id,
                        'active'     => 'N',
                    ]);
                }

                // 4. Cria também a autorização no perfil Administrador pois este sempre recebe todas Actions
                // Authorization::create([
                //     'profile_id' => 1,              // 1 = Administrador
                //     'action_id'  => $action->id,
                //     'active'     => 'N',
                // ]);
            });

            return response()->json([
                'sucesso' => $request->entity_id,
                'message' => 'Nova Ação e Autorização inserida com sucesso.'
            ], Response::HTTP_OK);

            // Anterior com Erro de lógica usando attach()
                // Não replica autorizações para a nova Ação criada na Entidade nos Perfis de Acesso que já tinham a Entidade
                // $profiles = Profile::where('system_id', $systemId)->get();  // only for the same System
                // dd($profiles);

                // foreach ($profiles as $profile) {
                //     $action->profiles()->attach($profile->id, ['active' => 'N', 'created_at' => now(), 'updated_at' => now()]);
                // }

                // return response()->json(['sucesso' => $action->entity_id, 'message' => 'Nova Ação e Autorização inserida com sucesso.'], Response::HTTP_OK);

        } catch (Exception $e) {

            return response()->json([
                'sucesso' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($action);
    }     

    public function update(ActionRequest $request)
    {
        $action = Action::findOrFail($request->id);
        $action->update($request->only(['entity_id', 'action', 'route', 'description']));

        return response()->json($action);
    }     

    public function destroy(Request $request)
    {        
        try {
            $action = Action::findOrFail($request->id);

            // the canDelete() is on Model
            if ($action->canDelete()) {
                $action->delete();
                return response()->json($action);
            } else {
                return Response()->json(['message'=>'<b>Impossível EXCLUIR</b> porque há Autorizações ativas (SIM)'], Response::HTTP_FORBIDDEN); //422
            }
            
        } catch (Exception $e) {        

            // Get all gerenical errors
            return response()->json([
                'sucesso' => false,
                'message' => ($e->getCode() == 23000 ? '<b>Impossível EXCLUIR</b> porque há registros relacionados. (SQL-1451).' : 'Houve um ERRO desconhecido! A Operação foi cancelada.'),
                'error' => ($e->getCode() == 23000 ? 'errorMessage1451' : 'errorMessage0000' ),
                'code' => $e->getCode(),
                // 'details' => $e->getMessage(),
            ], Response::HTTP_FORBIDDEN);            
        }

        return response()->json(['success' => true, 'message' => 'Operação realizada com sucesso.'], Response::HTTP_OK);
    }   
}
