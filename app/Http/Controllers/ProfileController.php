<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Authorization;
use App\Models\Entity;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\System;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Traits\ACLTrait;

class ProfileController extends Controller
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
            // $profiles = Sistema::all();                                          // Recupera as organizações
            // $profiles = SistemaResource::collection(Sistema::all());         // Recupera as organizações aplicando um Resource sobre a coleção add DT_RowId

            // $profiles = Sistema::with('sistemas')->where('id','>=', '1')->get();    // Recupera as organizações com a relação 'sistemas', filtrando por id >= 1
            // $profiles = Profile::with('system')->where('id','>=', '1')->get();    // Recupera as organizações com a relação 'sistemas', filtrando por id >= 1
            // $profiles = Sistema::with('sistemas')->where('id','=', request()->pessoa_id)->get();    // filtra pelo parametro request()->pessoa_id recebido no GET 

            // Retorna os dados no formato esperado pelo DataTables com data[]
            // return response()->json(['data' => $profiles]);            

            // retorna os dados customizados aplicando o SistemaResource() que automaticamente adiciona o data[] poderá retirar o 'sistemas' se não programado


            // option query using page filters
            // $query = Profile::where('id','>=', '1'); 
            // $query = Profile::with('system','systems')->where('id','>=', '1');
            $query = Profile::with('system')->where('id','>=', '1');
            
            // $query = Profile::with(['system', 'actions.entity'])->where('id','>=', '1'); 

            // if you received the filter page parameters apply the filter in where
            if ($request->input('filterSelect1')) {
                $query->where('system_id', $request->input('filterSelect1'));
            }            

            // execute the query with filter parameters
            $profiles = $query->get();

            // get the authorizations granted to the entity 
            $authorizations = $this->getAbilities('profile');   // using Trait
            // $authorizations = [
            //     'profile.index', 
            //     'profile.show',
            //     'profile.store',
            //     'profile.update',
            //     'profile.destroy',
            //     'profile.toggleEntity',
            // ];

            // add two atributtes to the Profile Collection: 'authorizations' and 'entities' granted to the Profile
            $profiles->each(function ($profile) use ($authorizations)  {    
                $profile->setAttribute('authorizations', $authorizations);
                $profile->setAttribute('entities', 
                    $profile->actions
                        ->filter(fn($action) => $action->entity->id >= 1)    // only No Base Entities (with id > 7)
                            ->map(fn($action) =>  ['id' => $action->entity->id, 'model' => $action->entity->model])
                            ->unique('id')  // only Entities uniques ID
                            ->values()
                );  // add the list of all Entities granted to the Profile
            });          


            $listTest = [];
            // $listTest = Profile::first()
            //         ->authorizations()  // Relacionamento de Authorizations no Profile
            //         ->whereHas('action', function ($query) { 
            //             $query->where('entity_id', '<=', 7); // Filtra apenas Authorizations que tenham Actions com entity_id <= 7
            //         })
            //         ->with('action') // Carrega apenas Actions filtradas
            //         ->get();

            return [
                'data' => $profiles,
                'authorizations' => $authorizations,
                'test' => $listTest,
            ];
        }

        // load entity configuration file from config/acl
        $fileConfigJson = config_path('acl/profile.json');
        $entityConfig = file_exists($fileConfigJson) ? json_decode(file_get_contents($fileConfigJson), false) : [];

        // send list options to view: column 'description' is used in <options>
        $systems = System::all()->sortBy('acronym')->map(function($system) {
            $system->description = $system->acronym;    // create description column from 'acronym'
            return $system;
        });        

        return view('acl/ProfileDatatable',['filterOptions1' => $systems, 'systems' => $systems, 'entityConfig' => $entityConfig]);
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

    public function store(ProfileRequest $request)
    {
        // $profile = Profile::Create(
        //     $request->only(['system_id', 'name', 'acronym', 'description', 'active'])
        // );  

        $exception = DB::transaction(function() use ($request) {
            try {

                // PASSO 1 - vamos inserir o Perfil
                $profile = Profile::Create(
                    $request->only(['system_id', 'name', 'acronym', 'description', 'active'])
                );  
                
                // PASSO 2 - vamos inserir todas as respectivas Autorizações das Entidades Básicas no novo Perfil de Acesso 
                //           Para isso replicamos as Autorizações do Perfil Padrão (id=1) Administrador
                $authorizations = Profile::first()
                    ->authorizations()  // Relacionamento de Authorizations no Profile
                    ->whereHas('action', function ($query) { 
                        // $query->where('entity_id', '<=', 7); // Filtra apenas Authorizations que tenham Actions com entity_id <= 7
                        $query->whereIn('entity_id', [5, 6, 7]); // Filtra apenas Actions default do Profile User
                    })
                    ->with('action') // Carrega apenas Actions filtradas
                    ->get()
                    ->map(function ($authorization) use ($profile) {
                        // Verifica se a Action associada contém a substring '.index' na coluna 'route' usando str_contains que não aceita coringas
                        // Verifica se a Action associada contém a substring '*.list*' na coluna 'route' usando expressão regular que aceita coringas
                        $active = (str_contains($authorization->action->route, '.index') 
                            || preg_match('/^.*\.list.*$/', $authorization->action->route)
                            || in_array($authorization->action->route, ['authorization.show','profile.show','user.show'] )
                            ) ? 'Y' : 'N';

                            // Inclui 10 default Authorization referentes à Actions das Entities Base ao Novo Profile
                            Authorization::Create(
                                [
                                    'profile_id' => $profile->id,                   // primary key of acl_profiles
                                    'action_id'  => $authorization->action_id,      // primary key of acl_actions
                                    'active'     => $active,                        // '*.index' or '*.list*' = active 'Y'
                                ]
                            );
                });       
                
                /*
                    Authorizations actives do Profile ? com a respectivas Action

                    SELECT *  FROM `acl_authorizations` 
                    INNER JOIN acl_actions ON acl_actions.id = acl_authorizations.action_id
                    WHERE `profile_id` = 4  
                    and acl_authorizations.active = 'Y'
                    ORDER BY acl_actions.route                
                
                */
            }
            catch(Exception $e) {
                throw new Exception('EUZ-PERFIL-Exception:' . $e);
            }
        });

        return Response()->json(is_null($exception) ? ['id' => $request->id] : $exception);        
        // return response()->json($profile);
    }     

    public function update(ProfileRequest $request)
    {
        $profile = Profile::findOrFail($request->id);
        $profile->update($request->only(['system_id','name', 'acronym', 'description', 'active']));

        return response()->json($profile);
    }     

    public function destroy(Request $request)
    {     
        try {
            $profile = Profile::findOrFail($request->id);

            // the canDelete() is on Model
            if ($profile->canDelete()) {
                $profile->delete();
                return response()->json($profile);
            } else {
                return Response()->json(['message'=>'Exception: Não é possível excluir o Perfil de Acesso, pois há Autorizações ativas (SIM)'], Response::HTTP_UNPROCESSABLE_ENTITY); //422
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

    /**
     * List System's Role
     */
    public function listSystems(Request $request)
    {
        $profileId = $request->id;

        // list all Systems with the granted column to Profile
        $systems = System::with(['roles' => function ($query) use ($profileId) {            
            $query->where('profile_id', $profileId );

            }])->get()->map(function ($system) {
                $system->granted = $system->roles->isNotEmpty() ? 'checked' : '';   // add collumn 'granted' with Y or N if the Profile has System
                return $system;
        });        

        return Response()->json($systems);
    }       

    /**
     * Assign ou Revoke a Systems to User
     */
    public function toggleSystem(Request $request)
    {
        try {
            // get the Profile
            $profile = Profile::find($request->profile_id);

            if (! $profile) {
                throw new Exception('Perfil de Acesso não encontrado.');
            }            

            // Atualiza os Systems atribuídos ao Perfil de Aeesso
            $profile->systems()->sync($request->systems); // Supondo que tenha um belongsToMany(System::class)
            
            return response()->json(['sucesso' => true, 'message' => 'Itens atribuídos com sucesso.'], Response::HTTP_OK);

        } catch (Exception $e) {

            // Get all gerenical errors 
            return response()->json([
                'sucesso' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            $entity = Entity::with('actions')->find($request->entity_id);
            // dd($entity->toArray());
            // dd($entity);

            if (!$entity) {
                throw new Exception('Exception: Entidade não encontrada.');
            }                        

            // revoke Authorizations from Entity on Profile
            // Para revogar é necessário que nenhuma Authorização esteja Y
            if ($request->operation == 'revokeEntity') {

                // Verifica se a Entity tem alguma Authorization Active em qualquer Perfil (Role)
                $hasActiveAuthorization = $entity->actions()
                    ->whereHas('authorizations', function ($query) use ($request) {
                        $query->where('profile_id', $request->role_id);
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
