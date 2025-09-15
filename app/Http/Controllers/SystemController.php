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
            $request->only(['name','acronym','url','icon','description','active'])
        ); 

        return response()->json($system);
    }     

    public function update(SystemRequest $request)
    {
        $system = System::findOrFail($request->id);
        $system->update($request->only(['name','acronym','url','icon','description','active']));

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
