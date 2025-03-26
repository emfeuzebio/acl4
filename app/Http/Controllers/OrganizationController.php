<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use Exception;
use Illuminate\Http\Response;
use App\Services\OrganizationService;
use App\Traits\ACLTrait; 

// EUZ xperi}encia com EmailService por terminar
use App\Services\EmailService;
use App\Mail\LoginNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{  
    use ACLTrait;

    protected $emailService;
    
    // public function __construct() 
    public function __construct(EmailService $emailService)
    {
        $this->middleware('auth');

        // EUZ
        $this->emailService = $emailService;
    }

    public function index() {

        if (request()->ajax()) {

            // $organizations = Organization::with('sistemas')->where('id','>=','1')->get();
            $organizations = Organization::where('id','>=','1')->get();

            // get the authorizations granted to the entity 
            // $authorizations = $this->getAbilities();  
            $authorizations = $this->getAbilities('organization');   // using Trait
            // delete after above implementation
            // $authorizations = [
            //     'organization.index',
            //     'organization.show',
            //     'organization.update',
            //     'organization.destroy',
            //     'organization.store',
            // ];

            // Add the abilities (authorizations) to the Collection for each organization: function (User $user) use ($authorization)
            $organizations->each(function ($organization) use ($authorizations)  {
                $organization->setAttribute('authorizations', $authorizations);
            });          

            return [
                'data' => $organizations,
                'authorizations' => $authorizations,
            ];

            // return [
            //     'data' => OrganizacaoResource::collection($organizations),
            //     'autorizacoes' => $this->getAbilities(),
            // ];
        }

        // load entity configuration file from config/acl
        $entityConfigJson = file_get_contents(config_path('acl/organization.json'));
        $entityConfig = json_decode($entityConfigJson, false);

        return view('acl/OrganizationDatatable', compact('entityConfig'));
    }

    /*
        Usa
        Implements SOLID principles
        Single Responsibility Principle (SRP)
        Encapsular as operações e garantir que o controller se concentre apenas em orquestrar o fluxo. 
        Isso segue o princípio de responsabilidade única (SRP) e permite que você faça a transação de maneira controlada.
    */
    public function store(OrganizationRequest $request, OrganizationService $organizationService) {

        try {
            // Chama o serviço para processar a operação

            // Opção quando recebe o ID do registro inserido e que enviar à view
            $organization = $organizationService->insert($request);
            return response()->json(['sucesso' => true, 'id' => $organization->id, 'message' => 'Operação realizada com sucesso!'], Response::HTTP_OK);

            // Opção quando vamos enviar a vies apenas TRUE, caso de Operação é complexa com vários passos
            // $organizationService->insert($request);            
            // return response()->json(['sucesso' => true, 'message' => 'Operação realizada com sucesso!'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['sucesso' => false, 'message' => 'Erro ao processar a operação!<br/> ' . $e->getMessage(),], Response::HTTP_BAD_REQUEST);
        }        
    }

    public function show(Request $request)
    {        
        $organization = Organization::where('id', $request->id)->first();
        $organization['ACLupdate'] = Gate::allows('organization.update');     // returns true if the User has permission to Update
        // $organization['ACLupdate'] = true;     // returns true if the User has permission to Update

        return response()->json($organization);
    }      

    public function update(OrganizationRequest $request)
    {
        $organization = Organization::findOrFail($request->id);
        $organization->update($request->only(['name', 'acronym', 'description', 'active']));

        $user = Auth::user();
        // dd($user);
        // Disparando o e-mail
        $this->emailService->sendLoginNotification($user);

        return response()->json($organization);
    }     

    public function destroy(Request $request)
    {        
        try {
            $organization = Organization::findOrFail($request->id);
            $organization->delete();
            
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