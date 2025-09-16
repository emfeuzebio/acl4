<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\NotifyUserMail;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Action;
use App\Models\Authorization;
use App\Models\Organization;
use App\Models\Profile;
use App\Models\System;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Traits\ACLTrait;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ACLTrait;

    public function __construct() 
    {
        $this->middleware('auth');
    }

    public function index() 
    {
        // dd(auth()->user()->can('veiculos.index'));
        
        if (request()->ajax()) {

            $users = User::where('id','>=', '1')->with(['profiles','systems','organizations'])->get();

            // get the authorizations granted to the entity 
            $authorizations = $this->getAbilities('user');   // using Trait

            // $authorizations = $this->getAbilities();  
            // $authorizations = [
            //     'user.show',
            //     'user.update',
            //     'user.destroy',
            //     'user.store',
            //     'user.store',

            //     'user.listRoles',
            //     'user.listSystems',
            //     'user.listOrganiz',

            //     'user.toggleRole',
            //     'user.toggleSystem',
            //     'user.toggleAuthorz',
            //     'user.toggleOrganiz',
            // ];

            $users->each(function ($users) use ($authorizations)  {  // Collection
                $users->setAttribute('authorizations', $authorizations);
            });          

            return [
                // 'data' => SistemaResource::collection($users),
                'data' => $users,
                'authorizations' => $authorizations,
            ];
        }

        // load entity configuration file from config/acl
        $fileConfigJson = config_path('acl/user.json');
        $entityConfig = file_exists($fileConfigJson) ? json_decode(file_get_contents($fileConfigJson), false) : [];

        // Loads all active organizations into the select
        $organizations = Organization::where('active','Y')->get();

        return view('acl/UserDatatable',['entityConfig' => $entityConfig, 'organizations' => $organizations]);
    }

    public function show(Request $request)
    {        
        $user = User::where('id', $request->id)->first();
        $user['ACLupdate'] = Gate::allows('user.update');     // returns true if the User has permission to Update
        // $user['ACLupdate'] = 'user.show';     // returns true if the User has permission to Update

        return response()->json($user);
    }      

    public function store(UserRequest $request)
    {
        $user = DB::transaction(function() use ($request) {
            try {
                // STEP 1 - Lest's insert the User
                $user = User::create([
                    // 'organization_id' => $request->organization_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password), // Criptografa a senha
                    'active' => 'Y',
                ]); 

                // STEP 2 - Let's insert thes recpectives Profiles Base
                // Vamos conceder o Perfil de Acesso padrão (3-Usuário) ao Usuário recém criado
                // TERMINAR Porém sabemos na Organização onde o User foi Criado Há vários Sistemas, em cada Sistema há vários Perfis

                    // Obtenha a organização à qual o usuário foi associado

                        /* Chat gpt

                        laravel 10 Eloquent
                        Uma Organization tem vários Systems e tem vários Profiles.
                        Um User pertence a uma Organization ID = 1.
                        Após inserir um novo User preciso conceder a este User todos os Profiles de todos os Systems da Organization do User.
                        Como fazer de forma clara e elegante

                        */

                        // $organization = Organization::findOrFail($request->organization_id);

                        // // Obtenha todos os sistemas dessa organização
                        // $systems = $organization->systems;

                        // // Para cada sistema, obtemos os profiles e associamos ao usuário
                        // foreach ($systems as $system) {
                        //     $profiles = $system->profiles;  // Obtém todos os profiles do sistema

                        //     // Associe todos os profiles desse sistema ao novo usuário
                        //     foreach ($profiles as $profile) {
                        //         $user->profiles()->attach($profile->id);  // Método attach para relacionar o usuário com o profile
                        //     }
                        // }

                        // $user->profiles()->sync($profiles->pluck('id')->toArray());


                    // print_r($profiles->toJson());
                    // die();
                    // return false;  


                // foreach($actions as $key => $action) {
                //     Action::create([
                //         'entity_id' => $entity->id,
                //         'action' => $descriptions[$key] . ' ' . $entity->model,
                //         'route' => strtolower($entity->model) . '.' . strtolower($action),
                //         'description' => $descriptions[$key] . ' ' . $entity->model,
                //     ]);  
                // } 
            }
            catch(Exception $e) {
                throw new Exception('EUZ-USER-Exception:' . $e);
            }

            return $user;
        });                                           

        return response()->json($user);        
    }     

    public function update(UserRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->id);
            $currentActive = $user->active;

            // Verifica se uma nova foto foi enviada
            $novaFoto = null;
            $caminhoFotoAntiga = $user->photo;

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'users/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public', $filename);    // Armazena no disco
                $novaFoto = $filename;
                $user->photo = $novaFoto;
            }

            // Remove a foto se solicitado
            if ($request->input('photo_removed') == '1') {
                $user->photo = null;
            }

            // Atualiza os campos comuns (menos senha)
            $user->update($request->only(['name','email','phone','active']));

            // Atualiza a senha se foi enviada
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            DB::commit();

            // :: ATENÇÃO: Só aqui, após commit, manipulamos arquivos e enviamos email ::

            // Deleta a foto antiga se necessário
            if ($novaFoto && $caminhoFotoAntiga && !str_contains($caminhoFotoAntiga, 'avatar.jpg')) {
                Storage::delete('public/' . str_replace('storage/', '', $caminhoFotoAntiga));
            }

            if ($request->input('photo_removed') == '1' && $caminhoFotoAntiga && !str_contains($caminhoFotoAntiga, 'avatar.jpg')) {
                Storage::delete('public/' . str_replace('storage/', '', $caminhoFotoAntiga));
            }

            // Notificação por e-mail se o campo 'active' mudou
            if ($currentActive != $request->active) {
                if ($request->active == 'Y') {
                    $subject = "Ativação de Usuário.";
                    $text = "Sua conta de Usuário foi Ativada com sucesso.";
                } else {
                    // $subject = config('app.name') . " - Desativação de Usuário.";
                    $subject = "Desativação de Usuário.";
                    $text = "Sua conta de Usuário expirou ou foi Desativada.\n" .
                            "Caso necessite Ativar novamente, procure o Administrador.";

                    // Vamos revogar todos Tokens ativos do User que esta sendo Desativado
                    Token::where('user_id', $user->id)          // tokens do user atual
                        ->where('status', 'active')            // tokens ativos
                        ->update(['status' => 'revoked']);     // Alterar o status do token para "Revoked"
                }

                // Notifica o usuário por email
                Mail::to($user->email)->send(new NotifyUserMail(
                    $subject,
                    $user->name,
                    $text
                ));
            }

            return response()->json($user);
        
        } catch (\Throwable $e) {
            DB::rollBack();

            // Se nova foto foi salva, remove para evitar lixo
            if (isset($novaFoto)) {
                Storage::delete('public/' . str_replace('storage/', '', $novaFoto));
            }

            return response()->json([
                'sucesso' => false,
                'message' => 'Erro ao atualizar o usuário.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } 
    }

    public function updateProfile(Request $request)
    {
        // All User has authorization to update your profile
        // Na ACL v3 acrescentar upload de foto do User

        $request->validate([
            'name' => 'required|string|min:6',
            // 'image' => 'nullable|image|max:2048',
            // 'email' => 'required|string|email|max:255|unique:users,email' . $this->id,
            // 'password' => 'required|string|min:6|confirmed',
        ]);        

        // $user = Auth::user();
        // $user->name = $request->name;
        // $user->save();

        $user = User::findOrFail($request->id);
        $user->update($request->only(['name']));
        // $user->update($request->only(['organization_id','name', 'email', 'password', 'active']));

        return response()->json($user);        
    }    

    public function destroy(Request $request)
    {        
        try {
            $user = User::findOrFail($request->id);

            if ($user->photo && !str_contains($user->photo, 'avatar.jpg')) {
                Storage::delete('public/' . str_replace('storage/', '', $user->photo));
            }

            $user->delete();
            
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

    public function getProfiles()
    {
        return $this->profiles()->get(); // Retorna a coleção de perfis
    }    

    /**
     * List Role's User
     */
    public function listRolesOLD(Request $request)
    {
        // solução da versão ACL 1
        // $profiles = Profile::orderBy('id')->where('active', 'Y')->orderBy('name')->get();
        // print_r($profiles);

        // Solution to Version ACL2 
        $userId = $request->id;

        // list all pofiles    
        $profiles = Profile::with(['users' => function ($query) use ($userId) {
            $query->where('user_id', $userId );

            }])->get()->map(function ($profile) {
                // add collumn 'granted' with Y or N if the User has Role
                $profile->granted = $profile->users->isNotEmpty() ? 'checked' : '';
                return $profile;
        });        

        return Response()->json($profiles);        
    }   

    public function listRoles(Request $request)
    {
        $userId = $request->id;

        $profiles = Profile::where(function($query) use ($userId) {
                $query->whereHas('system.users', function($q) use ($userId) {
                    $q->where('users.id', $userId);
                })
                ->orWhere('id', 1); // Exceção para o Profile ID 1
            })
            ->with(['users' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->get()
            ->map(function ($profile) {
                $profile->granted = $profile->users->isNotEmpty() ? 'checked' : '';
                return $profile;
            });

        return response()->json($profiles); 
        
    }    

    /**
     * List System's User 
     */
    public function listSystems(Request $request)
    {
        // Solution to Version ACL2 
        $userId = $request->id;

        // list all pofiles    
        $systems = System::with(['users' => function ($query) use ($userId) {
            $query->where('user_id', $userId );

            }])->get()->map(function ($system) {
                // add collumn 'granted' with Y or N if the User has Role
                $system->granted = $system->users->isNotEmpty() ? 'checked' : '';
                return $system;
        });        

        return Response()->json($systems);        
    }   

    /**
     * List Active Authorization's User without repetitions lines
     */
    public function listActiveAuth(Request $request)
    {
        $userId = $request->id; 

        $authorizations = Action::whereHas('authorizations', function ($query) use ($userId) {
            $query->where('active', 'Y')
                ->whereHas('profile.users', function ($q) use ($userId) {
                    $q->where('users.id', $userId);
                });
        })
        // ->select('id', 'name', 'entity_id')
        // ->with('entity:id,name')
        // ->with(['authorizations'
        ->with([
            'entity:id,model',
            'authorizations.profile.system:id,acronym',        // apenas colunas necessárias de System
            // 'profile',        // apenas colunas necessárias de System
        ])
        ->distinct()
        ->get()
        // ->sortBy('entity.model')        // Reordena
        // ->values()                     // Reindexa os índices da coleção
        ;

        return Response()->json($authorizations);        

        // $authorizations = Authorization::where('active', 'Y')
        //     ->whereHas('profile.users', function ($query) use ($userId) {
        //         $query->where('users.id', $userId);
        //     })
        //     ->with(['action.entity', 'profile.systems'])
        //     ->get()
        //     ->unique('action_id')           // Evita repetições de autorizações na mesma Action
        //     ->sortBy('action.entity.model') // Reordena
        //     ->values();                     // Reindexa os índices da coleção


        // $authorizations = User::find($userId)
        //     ->profiles()
        //     ->where('active', 'Y')
        //     // ->with(['authorizations'])       // Eager load para as authorizations
        //     ->with(['authorizations' => function ($query) {
        //         $query->where('active', 'Y');   // Filtra as Authorizations com 'active' igual a 'Y'
        //         $query->with('action.entity');
        //     }])
        //     ->with('system') // Eager load dos Systems associados aos Profiles            
        //     ->get()
        //     ->flatMap(function ($profile) {
        //         return $profile->authorizations; // Acessando as authorizations de cada profile
        //     })
        //     ->unique('id'); // Garantir que as authorizations sejam únicas pelo campo 'id'    

        // $authorizations = Authorization::where('active','Y')
        //     ->select('id','profile_id','action_id','active')
        //     ->whereHas('profile.users', function ($query) use ($userId) {
        //         $query->where('users.id', $userId);
        //     })
        //     // ->with(['action.entity'])   // todas colunas das Action e da Entity     
        //     // ->with([
        //     //     'action', // todas colunas da Action
        //     //     // 'action:id,entity_id,action,route', // apenas colunas necessárias da Action
        //     //     'action.entity',           // todas colunas da Entity
        //     //     // 'action.entity:id,model',           // apenas colunas necessárias da Entity
        //     // ])
        //     ->with([
        //         'profile.users',
        //         'profile.system:id,acronym',        // apenas colunas necessárias de System
        //         'action:id,entity_id,action,route', // apenas colunas necessárias da Action
        //         'action.entity:id,model',           // apenas colunas necessárias da Entity
        //     ])
        //     ->get();

    }   

    /**
     * List Organization's User
     */
    public function listOrganiz(Request $request)
    {
        $userId = $request->id;

        // list all Organizations    
        $organizations = Organization::where('active', 'Y')->with(['users' => function ($query) use ($userId) {
            $query->where('user_id', $userId );

            }])->get()->map(function ($organization) {
                // add collumn 'granted' with Y or N if the User has Organization
                $organization->granted = $organization->users->isNotEmpty() ? 'checked' : '';
                return $organization;
        });        

        return Response()->json($organizations);
    }   

    /**
     * Assign ou Revoke a Roles (Profiles) to User
     */
    public function toggleRole(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            if (!$user) {
                throw new Exception('Usuário não encontrado.');
            }            
    
            if ($user->active == 'N') {
                throw new Exception('Impossível Conceder/Revogar Perfis a um Usuário Não Ativo.');
            }            

            $role = Profile::findOrFail($request->role_id);
            $roleName = $role->name;
    
            if ($request->operation == 'assignRole') {
                $user->profiles()->attach($request->role_id);   // assign de Role (attach)

                $subject = "Concessão de Perfil de Acesso a Usuário.";
                $text = htmlspecialchars("O Perfil de Acesso '{$roleName}' lhe foi Concedido com sucesso.");    // Usar assim para não quebrar Email
            } elseif ($request->operation == 'revokeRole') {
                $user->profiles()->detach($request->role_id);   // Revoke the a Role (detach)

                $subject = "Revogação de Perfil de Acesso a Usuário.";
                $text = htmlspecialchars("O Perfil de Acesso '{$roleName}' lhe foi Revogado com sucesso.");
            } 

            // Notifica o usuário por email
            Mail::to($user->email)->send(new NotifyUserMail(
                $subject,
                $user->name,
                $text
            ));        
                
            return response()->json(['sucesso' => true, 'message' => 'Salvo com sucesso.'], Response::HTTP_OK);

        } catch (Exception $e) {

            // Get all gerenical errors 
            return response()->json([
                'sucesso' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    

    /**
     * Assign ou Revoke a Systems to User
     */
    public function toggleSystem(Request $request)
    {
        try {
            // get the User
            $user = User::find($request->user_id);

            if (!$user) {
                throw new Exception('Usuário não encontrado.');
            }            

            $system = System::find($request->system_id);
            
            if (!$system) {
                throw new Exception('Sistema não encontrado.');
            }            
            $systemName = $system->name;

    
            if ($request->operacao == 'assignSystem') {

                // assign de System (attach)
                $user->systems()->attach($request->system_id);

                $subject = "Concessão de Acesso a Sistema.";
                $text = "O Administrador lhe concedeu acesso ao Sistema '{$systemName}' com sucesso.";

            } elseif ($request->operacao == 'revokeSystem') {

                // Revoke the a System (detach)
                $user->systems()->detach($request->system_id);

                $subject = "Revogação de Acesso a Sistema.";
                $text = "O Administrador revogou seu acesso ao Sistema '{$systemName}' com sucesso.";
            } 

            // Notifica o usuário por email
            Mail::to($user->email)->send(new NotifyUserMail(
                $subject,
                $user->name,
                $text
            ));        
            
            return response()->json(['sucesso' => true, 'message' => 'Salvo com sucesso.'], Response::HTTP_OK);

        } catch (Exception $e) {

            // Get all gerenical errors 
            return response()->json([
                'sucesso' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    

    /**
     * Assign ou Revoke a Action to Profile only save 'Y' or 'N' in the column 'active' of the table 'acl_authorizations'
     */
    public function toggleAuthorz(Request $request)
    {
        try {
            $authorization = Authorization::find($request->authorization_id);
            // print_r($authorization->toJson());
            // die();

            if (!$authorization) {
                throw new Exception('Autorização não encontrada.');
            }    

            $authorization->active = ($request->operation == 'assignAction' ? 'Y' : 'N' );
            $authorization->save();
            
            return response()->json(['sucesso' => true, 'message' => 'Salvo com sucesso.'], Response::HTTP_OK);

        } catch (Exception $e) {

            // Get all gerenical errors
            return response()->json([
                'sucesso' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    

    /**
     * Assign ou Revoke a Organizations to User 
     */
    public function toggleOrganiz(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            if (! $user) {
                throw new Exception('Usuário não encontrado.');
            }    

            // Atualiza as organizações atribuídas ao usuário
            // Isso irá remover todas as organizações atribuídas anteriormente e adicionar as novas
            // sync($request->organizations) FUNCIONA se o AJAX enviar um array de IDs de organizações
            // a view deve ter um campo de seleção múltipla com o nome 'organizations[]' e um botão de envio
            $user->organizations()->sync($request->organizations); // Supondo que tenha um belongsToMany(Organization::class)
            
            return response()->json(['sucesso' => true, 'message' => 'Salvo com sucesso.'], Response::HTTP_OK);

        } catch (Exception $e) {

            // Get all gerenical errors
            return response()->json([
                'sucesso' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    

    /**
     * Execute Reset DataBase (Truncate, Migrate and Seed) the DataBase to initial charge
     */
    public function refreshDB(Request $request)
    {
        $output = '';

        try {
            /**
             * Aqui seria bom validar novamente se é SuperAdmin
             */
                // $user = User::find($request->user_id);

                // if (! $user) {
                //     throw new Exception('Usuário não encontrado.');
                // }    
 
            $exitCode = Artisan::call('migrate:fresh --force');
            $output .= Artisan::output();                   // Capturando a saída
    
            // Se o migrate:fresh foi bem-sucedido, rodar o seeder
            if ($exitCode === 0) {
                // Rodando o seeder para popular o banco de dados
                $exitCode = Artisan::call('db:seed --force');
                $output .= "<br/><br/>\n" . Artisan::output();  // Capturando a saída do seeder
            }
    
            // Verificar se o comando foi bem-sucedido (o código de saída 0 significa sucesso)
            if ($exitCode === 0) {
                return redirect()->back()->withErrors('status', "Banco de Dados resetado com sucesso! <br/><br/>\n" . $output);
            } else {
                return redirect()->back()->withErrors('error', "Falha ao executar a operação! <br/><br/>\n" . $output);
            }

        } catch (Exception $e) {

            return redirect()->back()->with('error', "Falha ao executar a operação! " . $e->getMessage() . "" . $output);
        }
    }    
}
