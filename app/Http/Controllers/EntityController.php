<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EntityRequest;
use App\Models\Action;
use App\Models\Authorization;
use App\Models\Entity;
use App\Models\Profile;
use App\Models\System;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Traits\ACLTrait;
use Exception;
use Illuminate\Support\Facades\Gate;

class EntityController extends Controller
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

            /**
             * Forma para usar Datatables com serverSide: false e sem data[]
             *  com dataSrc: "" no ajax que não necessida do aoData[]
             */
            // $entities = Sistema::all();                                          // Recupera as organizações
            // $entities = SistemaResource::collection(Sistema::all());         // Recupera as organizações aplicando um Resource sobre a coleção add DT_RowId
            // retorna os dados customizados aplicando o SistemaResource() que automaticamente adiciona o data[] poderá retirar o 'sistemas' se não programado

            // option query using page filters
            // $query = Entity::with('system')->where('id','>=', '1'); 

            // obtem a lista de Perfis do User corrente, se tiver o Perfil ID = 1 Administrador, mostra inclusive Entidades Base
            $profiles = auth()->user()->profiles;
            $userProfileIds = auth()->user()->profiles->pluck('id')->toArray();
            $profileFilter = (in_array(1,$userProfileIds) ? 1 : 8);     // ID = 1 Administrador
            // dd($userProfileIds);


            $query = Entity::where('id','>=', $profileFilter); 

            // if you received the entity_id parameter apply the filter in where
            // if ($request->input('system_id')) {
            //     $query->where('system_id', $request->input('system_id'));
            // }            

            // execute the query
            $entities = $query->get();

            // get the authorizations granted to the entity 
            // $authorizations = $this->getAbilities();  
            $authorizations = $this->getAbilities('entity');   // using Trait

            // $authorizations = [
            //     'entity.show',
            //     'entity.update',
            //     'entity.destroy',
            //     'entity.store',
            //     'entity.store',
            // ];

            $entities->each(function ($entity) use ($authorizations)  {  // Collection
                $entity->setAttribute('authorizations', $authorizations);
            });          

            return [
                'data' => $entities,
                'authorizations' => $authorizations,
            ];
        }

        // load entity configuration file from config/acl
        $fileConfigJson = config_path('acl/entity.json');
        $entityConfig = file_exists($fileConfigJson) ? json_decode(file_get_contents($fileConfigJson), false) : [];

        // send list options to view: column 'description' is used in <options>
        $systems = System::all()->sortBy('acronym')->map(function($system) {
            $system->description = $system->acronym;    // create description column from 'acronym'
            return $system;
        });        

        return view('acl/EntityDatatable',['filterOptions1' => $systems, 'systems' => $systems, 'entityConfig' => $entityConfig]);
    }

    public function show(Request $request)
    {        
        $entity = Entity::with('actions')->where('id', $request->id)->first();
        $entity['ACLupdate'] = Gate::allows('entity.update');     // devolve true se o User tem permissão para Update
        // $entity['ACLupdate'] = true;     // returns true if the User has permission to Update

        return response()->json($entity);
    }      

    public function store(EntityRequest $request)
    {
        $entity = DB::transaction(function() use ($request) {
            try {

                // STEP 1 - Lest's insert the Entity
                $entity = Entity::Create([
                    // 'system_id' => $request->system_id, 
                    'model' => trim(ucwords(strtolower(str_replace(' ', '', $request->model)))), 
                    'table' => trim(strtolower(str_replace(' ', '', $request->table))), 
                    'description' => trim(ucwords(strtolower($request->description))),
                    'active' => $request->active,
                    ]
                );

                // STEP 2 - Let's insert thes recpectives Action's Base to Action children
                $actions = ['index','show','store','update','destroy'];
                $descriptions = ['List','Show','Insert','Update','Delete'];

                foreach($actions as $key => $action) {
                    Action::create([
                        'entity_id' => $entity->id,
                        'action' => $descriptions[$key] . ' ' . $entity->model,
                        'route' => strtolower($entity->model) . '.' . strtolower($action),
                        'description' => $descriptions[$key] . ' ' . $entity->model,
                    ]);  
                } 
            }
            catch (Exception $e) {
                throw new Exception('EUZ-ACTION-Exception:' . $e);
            }

            return $entity;
        });                                           

        return response()->json($entity);
    }     

    public function update(EntityRequest $request)
    {
        $entity = Entity::findOrFail($request->id);
        $entity->update($request->only('system_id', 'model', 'table', 'description', 'active'));

        return response()->json($entity);
    }     

    public function destroy(Request $request)
    {        
        // The Bases Entities (id=[1-7]) cannot edit or delete
        if($request->id <= 7) {
            return Response()->json(['message'=>'<b>Impossível EXCLUIR</b> uma Entidade Básica'], Response::HTTP_UNPROCESSABLE_ENTITY); //422
        }

        try {
            $entity = Entity::find($request->id);

            // the canDelete() is on Model
            if ($entity->canDelete()) {
                $entity->delete();
                return response()->json($entity);
            } else {
                return Response()->json(['message'=>'<b>Impossível EXCLUIR</b> a Entidade, pois há Autorizações ativas (SIM)'], Response::HTTP_UNPROCESSABLE_ENTITY); //422
            }

        } catch(Exception $e) {
            // throw new \Exception('EUZ-ACTION-Exception:' . $e);

            // Get all gerenical errors
            return response()->json([
                'sucesso' => false,
                'message' => ($e->getCode() == 23000 ? '<b>Impossível EXCLUIR</b> porque há registros relacionados. (SQL-1451).' : 'Houve um ERRO desconhecido! A Operação foi cancelada.'),
                'error' => ($e->getCode() == 23000 ? 'errorMessage1451' : 'errorMessage0000' ),
                'code' => $e->getCode() . ' - ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        

        return Response()->json($entity);
    }   

    public function listActions(Request $request)
    {        
        $actions['data'] = Action::where('entity_id',$request->id)->get();
        $actions['authorizations']['ACLdestroy'] = Gate::allows('action.destroy');     // devolve true se o User tem permissão para destroy
        $actions['authorizations']['ACLstore'] = Gate::allows('action.store');      // devolve true se o User tem permissão para store
        return Response()->json($actions);
    } 

    public function listAuthorzs(Request $request)
    {        
        // FUNCIONANDO recupera lista de Authorization com sua Action
        $authorizations = Authorization::with('profile','action.entity')->where('profile_id', $request->role_id)
            ->whereHas('action', function ($query) use ($request) {
                $query->where('entity_id', $request->entity_id);
        })->get();        

        return Response()->json($authorizations);
    } 

    public function listEntities(Request $request)
    {        
        // $entities = Entity::where('system_id',$request->system_id)->orderBy('id')->get();
        $entities = Entity::orderBy('id')->get();
        $profileId = $request->profile_id;

        // get list of entities granted to Profile (Role)
        $entitiesWithGranted = $entities->map(function($entity) use ($profileId) {

            $granted = $entity->actions()->whereHas('profiles', function($query) use ($profileId) {
                $query->where('acl_profiles.id', $profileId);
                    //   ->where('acl_authorizations.active', 'Y'); 
            })->exists() ? 'Y' : 'N';
        
            $entity->granted = $granted;        // add granted column
            return $entity;
        });        
        
        return Response()->json($entities);
    } 

    /**
     * Assign ou Revoke a Roles (Profiles) to User
     */
    public function toggleEntity(Request $request)
    {
        try {
            // get the Profile (Role)
            $profile = Profile::find($request->role_id);
            $profileId = $request->role_id;

            if (!$profile) {
                throw new Exception('Exception: Perfil de Acesso (Role) não encontrado.');
            }            

             // get the Entity
            $entity = Entity::find($request->entity_id);

            if (!$entity) {
                throw new Exception('Exception: Entidade não encontrada.');
            }                        

            // revoke Authorizations from Entity on Profile
            // Para revogar é necessário que nenhuma Authorização esteja Y
            if ($request->operation == 'revokeEntity') {

                // Verifica se a Entity tem alguma Authorization Active em qualquer Perfil (Role)
                $hasActiveAuthorization = $entity->actions()
                    ->whereHas('authorizations', function ($query) {
                        $query->where('active', 'Y');
                })->count();
                // })->exists();

                    // para ver os dados acima, executar esta consulta e ver no console
                    // $contActive = 0;
                    // $actions = $entity->actions()->where('entity_id', $request->entity_id)->with('authorizations')->get();

                    // foreach ($actions as $action) {
                    //     echo "Ação: " . $action->action . "<br>"; // Exemplo de exibição do nome da ação
                    //     foreach ($action->authorizations as $authorization) {
                    //         echo "Autorização ativa: " . $authorization->id . " no Profile: " . $authorization->profile_id . ' - ' . $authorization->active . "<br>";
                    //     }
                    // }
                    // // print_r($actions->toArray());
                    // die($contActive);

                // Se a Entity tem pelo menos uma Authorization ativa seja em qual for o Perfil de Acesso, NÃO permite revogar
                if ($hasActiveAuthorization) {
                    throw new Exception('<b>IMPOSSÍVEL revogar!</b><br/> Há ' . $hasActiveAuthorization . ' Ações Autorizadas em algum Perfil de Acesso.<br/><br/>Desautorize todas as Ações da Entidade antes de revogar.');
                } 

                // Como a Entity não tem nenhuma Authorization ativa, podemos
                // deletar todas as Authorizations associadas a esta Entity
                $entity->actions()->each(function ($action) use ($profileId) {
                    // deletar todas as Authorizations dessa Action que sejam do Perfil corrente
                    $action->authorizations()->where('profile_id', $profileId)->delete();
                });

                $message = "<b>Entidade Revogada!</b><br/> e todas Autorizações da Entidade ID = " . $request->entity_id . " pertencente ao Perfil ID = {$profileId} foram removidas da Entidade.";
                return response()->json(['sucesso' => true, 'message' => $message], Response::HTTP_OK);
            }

            // grant Authorizations to Entity on Profile
            if ($request->operation == 'assignEntity') {

                // STEP 1 - Let's delete all Authorizations from Entity on Profile
                $entity->actions()->each(function ($action) use ($profileId) {
                    $action->authorizations()->where('profile_id', $profileId)->delete();
                });                

                // STEP 2 - Let's insert for each Action from Entity an Authorizations on Profile
                $entity->actions()->each(function ($action) use ($profileId) {

                    // for default, only route '---.index' receives Authorization Active
                    // $active = strpos($action->route, '.index') !== false ? 'Y' : 'N';
                    $active = 'N';  // for default, all routes receives Authorization Inactive

                    // create a new Authorization with the necessary data
                    $action->authorizations()->create([
                        'profile_id' => $profileId,
                        'active' => $active
                    ]);
                });                

                return response()->json(['sucesso' => true, 'message' => 'Entidade concedida ao Perfil de Acesso com sucesso.'], Response::HTTP_OK);
            }

        } catch (Exception $e) {

            // Get all gerenical errors
            return response()->json([
                'sucesso' => false,
                'message' => ($e->getCode() == 23000 ? '<b>IMPOSSÍVEL inserir registros duplicadas! (SQL-1062)</b><br/>As Autorizações já existem.' : $e->getMessage()),
                'error' => ($e->getCode() == 23000 ? 'errorMessage1062' : 'errorMessage0000' ),
                'code' => $e->getCode(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);            
        }
    }   
}
