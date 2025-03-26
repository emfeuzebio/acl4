<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SystemRequest;
use App\Models\Action;
use App\Models\Authorization;
use App\Models\Organization;
use App\Models\Profile;
use App\Models\System;
use App\Models\User;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Traits\ACLTrait;

class SystemController extends Controller
{
    use ACLTrait;

    public function __construct() 
    {
        $this->middleware('auth');
    }

    public function index() {

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
            // $systems = Sistema::all();                                          // Recupera as organizações
            // $systems = SistemaResource::collection(Sistema::all());         // Recupera as organizações aplicando um Resource sobre a coleção add DT_RowId

            // $systems = Sistema::with('sistemas')->where('id','>=', '1')->get();    // Recupera as organizações com a relação 'sistemas', filtrando por id >= 1
            $systems = System::where('id','>=', '1')->get();    // Recupera as organizações com a relação 'sistemas', filtrando por id >= 1
            // $systems = Sistema::with('sistemas')->where('id','=', request()->pessoa_id)->get();    // filtra pelo parametro request()->pessoa_id recebido no GET 

            // Retorna os dados no formato esperado pelo DataTables com data[]
            // return response()->json(['data' => $systems]);            

            // retorna os dados customizados aplicando o SistemaResource() que automaticamente adiciona o data[] poderá retirar o 'sistemas' se não programado


            // get the authorizations granted to the entity 
            // $authorizations = $this->getAbilities('system');  
            $authorizations = $this->getAbilities('system');   // using Trait

            // $authorizations = [
            //     'system.show',
            //     'system.update',
            //     'system.destroy',
            //     'system.store',
            //     'system.store',
            // ];

            $systems->each(function ($system) use ($authorizations)  {  // Collection
                $system->setAttribute('authorizations', $authorizations);
            });          

            return [
                'data' => $systems,
                'authorizations' => $authorizations,
            ];

            // return [
            //     'data' => SistemaResource::collection($systems),
            //     'autorizacoes' => $this->getAbilities(),
            // ];

            // retorna SEM customizar os dados e SEM o data[] exigindo que a diretiva dataSrc: "" no ajax
            // return response()->json($systems);     
        }

        // load entity configuration file from config/acl
        $fileConfigJson = config_path('acl/system.json');
        $entityConfig = file_exists($fileConfigJson) ? json_decode(file_get_contents($fileConfigJson), false) : [];

        // envia lista para view
        $organizations = Organization::all()->sortBy('acronym');

        // return view('acl/SystemDatatable',['entityConfig' => $entityConfig]);
        return view('acl/SystemDatatable',['organizations'=> $organizations, 'entityConfig' => $entityConfig]);
    }

    public function show(Request $request)
    {        
        $system = System::where('id', $request->id)->first();
        $system['ACLupdate'] = Gate::allows('system.update');     // devolve true se o User tem permissão para Update
        // $system['ACLupdate'] = true;     // returns true if the User has permission to Update

        return response()->json($system);
    }      

    public function store(SystemRequest $request)
    {
        $system = System::Create(
            $request->only(['name', 'acronym', 'description', 'active'])
        ); 

        return response()->json($system);
    }     


    public function storeAPAGAR(SystemRequest $request)
    {
        /**
         * Criar um novo Sistema, sugnifica começar do Zero uma nova ACL com poucas exceções.
         * É necessário disparar as seguintes ações
         * 
         * Ao inserir um novo Sistema temos que:
         * 
         *  1 - Pegar o dados do SISTEMA Modelo ID = 1
         *  2 - Criar o novo SISTEMA
         *  3 - Replicar as ENTIDADES Base: [Organizations, Systems, Entities, Actions, Authorizations, Profiles, Users] filhas do SISTEMA
         *  4 - Replicar as AÇÕES filhas de cada ENTIDADE
         *  5 - Replicar os PERFIS de ACESSO (ROLES) Base [Administrator, Manager e User] (pois cada Sistema tem os seus)
         *  6 - Replicar as concessões dos USUÁRIOS Base aos novos PERFIS Base inicias (muitos para muitos) assim:
         *          User [Administrator] > recebe todos Perfis
         *          User [Maneger] > recebe apenas o Perfil de Manager
         *          User [User] > recebe apenas o Perfil de User
         *  7 - Replicar as concessões dos USUÁRIOS Base ao novo SISTEMA (muitos para muitos)
         *  8 - Replicar as respectivas AUTORIZAÇÕES que relacionam Ações com Profiles (muitos para muitos)
         *          As Authorizations para o Perfil de Administrator são todas 'Y'
         *          As Authorizations para os Perfis Manager e User são 'N' exceto as rotas ['index','show','user.listProfiles','user.listSystems']
         */

        $system = DB::transaction(function() use ($request) {
            try {

                // STEP 1: get the default base system ID = 1 (baseSystem) with its related entities and actions
                    $baseSystem = System::with('entities.actions')->find(1);

                // STEP 2 - insert the new System
                    $system = System::Create(
                        $request->only(['organization_id', 'name', 'acronym', 'description', 'active'])
                    ); 
                
                // FUNCIONANDO TUDÃO
                // baseSystem iS the default System ID = 1. This has all initial data to a new System
                // STEP 3-4 - Let's replicate the Entities and their respective Actions
                    foreach ($baseSystem->entities as $entity) {

                        // Criar uma nova Entity associada ao novo System
                        // $system = $baseSystem->entities()->create([
                        //     'name' => $entity->name,  // ou qualquer outro campo que precisa ser copiado
                        //     // Outros campos
                        // ]);

                        // STEP 3 - replicate the one Entity each loop
                        $newEntity = $entity->replicate();
                        $newEntity->system_id = $system->id;  // Associate a new Entity to the new System
                        $newEntity->save();

                        // STEP 4 - replicate the Actions to a new Entity
                        foreach ($entity->actions as $action) {

                            $newAction = $action->replicate();                                
                            $newAction->entity_id = $newEntity->id;  // associeate the new Action to the new Entity
                            $newAction->save();
                        }      
                    }      

                /**
                 * Options tested
                 */    
                // FUNCIONANDO
                // Solucion using createMany()
                    // // Passo 3: cria um array de arrays com as Entities (filhas) do novo pai (System), replicando os filhos do Pai base ID = 1
                    // $newEntities = $baseSystem->entities->map(function ($filho) use ($system) {
                    //     return [
                    //         'system_id' => $system->id,
                    //         'model' => $filho->model,
                    //         'table' => $filho->table,
                    //         'description' => $filho->description,
                    //         'active' => $filho->active,
                    //     ];
                    // })->toArray();
                    // $system->entities()->createMany($newEntities);
                    
                // FUNCIONA para o filhos (Entities) - Mas para o netos não deu certo!
                // Using createMany to insert to enter data at once
                    // Here we use a closure function to replicate the Base Entities with id <= 7
                    // $system->entities()->createMany(
                    //     Entity::where('id', '<=', 7)->get()->map(function ($entity) use ($system) {
                    //         return [
                    //             'system_id' => $system->id,     // associating with the new Entity
                    //             'model' => $entity->model,
                    //             'table' => $entity->table,
                    //             'description' => $entity->description,
                    //             'active' => $entity->active,
                    //         ];
                    //     })->toArray()
                    // );                    

                // STEP 5 - Let's replicate the default Profiles (Roles) [1-Administrator, 2-Maneger and 3-User] to the new System
                    $profiles = Profile::whereIn('id', [1, 2, 3])->get();
                    foreach ($profiles as $profile) {

                        $newProfile = $profile->replicate();
                        $newProfile->system_id = $system->id;  // associating with the new System
                        $newProfile->save();
                    }

                // STEP 6 - Replicate the Base USER grants to the new initial Base PROFILES (many to many): Veículos

                    // Get new Profiles 
                    $newProfiles = Profile::where('system_id',$system->id)->get();

                    // attach the Users on each Profile
                    $newProfiles[0]->users()->attach([1]);          // 1st Profile Administrator only to User 1-Administrator
                    $newProfiles[1]->users()->attach([1,2]);        // 2nd Profile Manageer to Users 1-Administrator and 2-Manager
                    $newProfiles[2]->users()->attach([1,3]);        // 3rd Profile User Only to User 3-User

                    // DEBUG
                    // print_r($newProfiles->toJson());
                    // print_r($newProfiles->toArray());
                    // die();


                // STEP 7 - Let's granted (pivot) the new System to 3 base Users (1-Administrator, 2-Maneger and 3-User)
                $users = User::whereIn('id', [1, 2, 3])->get();
                $system->users()->attach($users->pluck('id'));      // let's associate the 3 default users to the new system


                // STEP 8 - Replicate the respective AUTHORIZATIONS that relate Entity and Action (many to many)


                    // vamos buscar as Ações recém criadas para o Novo System
                    $actions = Action::whereHas('entity.system', function($query) use ($system) {
                        $query->where('id', $system->id);
                    })->get();

                    // For Profile 2-Managem and 3-User, only this Actions below are actives 'Y', otherwise 'N'
                    $actionsAuthorized = ['index','show','user.listProfiles','user.listSystems'];                    
                    
                    // for each new Action, let's create a new Authorization: 1 to 1
                    foreach ($actions as $action) {

                        // Always are created 3 new default Profiles. For each new Profile, let's create a new Authorization
                        foreach ($newProfiles as $key => $profile) {

                            // if Profile is 1-Administrator, all Actions are authorized: actives 'Y'
                            if ($key == 0) {
                                $active = 'Y';  // authorized default 'Y'es
                            } else {
                                $active = 'N';  // authorized default 'N'o
                                foreach ($actionsAuthorized as $authorizedRoute) {
                                    if (str_contains($action->route, $authorizedRoute)) {
                                        $active = 'Y';      // If the route contains the substring, set active to 'Y' and exit the loop
                                        break; 
                                    }
                                }
                            }

                            Authorization::Create(
                                [
                                    'profile_id' => $profile->id,           // primary key of acl_profiles
                                    'action_id'  => $action->id,            // primary key of acl_actions
                                    'active'     => $active,                // authorized default 'Y'es
                                ]
                            ); 
                        }
                    }
            }
            catch(Exception $e) {
                throw new Exception('Exception generate: ' . $e);
            }
            return $system;     // return a Systema to closure function
        });                

        // return response()->json(['success' => true, 'message' => 'Operação realizada com sucesso.'], Response::HTTP_OK);
        return response()->json($system);
    }     

    public function update(SystemRequest $request)
    {
        $system = System::findOrFail($request->id);
        $system->update($request->only(['name', 'acronym', 'description', 'active']));

        return response()->json($system);
    }     

    public function destroy(Request $request)
    {        
        try {
            $system = System::findOrFail($request->id);
            $system->delete();
            
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
