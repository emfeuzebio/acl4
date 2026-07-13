<?php

namespace App\Http\Controllers\Auth;

use App\Mail\NotifyUserMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TokenResource;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;
use App\Models\System;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function __construct()
    {
        // nesse caso ['login', 'register'] não dependem de estar autenticado
        $this->middleware('auth:api', ['except' => [
            'login',
            'selectSystem',
            'forgotpassword',
            'resetPassword',
            'me',
            'register',
            'logout',
            'revoke',
            'refresh',
            'listTokens',
            'forceRefresh',
            'loginWithoutBD',
            ]
        ]);

        // atualizar o status dos tokens expirados
        Token::where('expires_at', '<', now())->update(['status' => 'expired']);
    }

    // Login COM token persistente em banco de dados
    public function login(Request $request)
    {
        try {

            return DB::transaction(function () use ($request) {

                // PASSO 1 - Autenticar usuário sem gerar token ainda
                $credentials = $request->only('email', 'password');
                $user = User::where('email', $credentials['email'])->first();

                if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                    return response()->json(['error' => 'Credenciais inválidas'], Response::HTTP_UNAUTHORIZED);   // 401
                }

                if ($user->active != 'Y') {
                    return response()->json(['error' => 'Usuário Não Ativo.'], Response::HTTP_UNAUTHORIZED);
                }

                if (! $user->systems->contains('id', $request->systemId)) {
                    return response()->json(['error' => 'Usuário ainda não têm Acesso a este Sistema.'], Response::HTTP_UNAUTHORIZED);
                }

                $countProfilesOnSystem = $user->profiles()->where('system_id', $request->systemId)->count();
                // echo "count: {$countProfilesOnSystem}";

                if ($countProfilesOnSystem == 0) {
                    return response()->json(['error' => 'Usuário ainda não têm Perfil de Acesso a este Sistema.'], Response::HTTP_UNAUTHORIZED);
                }

                // $profiles: Collection dos Profiles do usuário dentro daquele system
                // $profiles = $user->profiles()
                //     ->where('system_id', $request->systemId)
                //     ->get();                
                // dd($profiles);

                // PASSO 2 - Verifica se já existe um token ativo para o usuário
                $existingToken = Token::where('user_id', $user->id)
                    ->where('status', 'active')                     // Verifica se está ativo
                    ->where('expires_at', '>', Carbon::now())       // Verifica se ainda está válido
                    ->first();

                // PASSO 3 - Caso exista, reutiliza o mesmo, SENÃO cria um novo
                if ($existingToken) {
                    $token = $existingToken->token;     // Se já existir um token válido, reutiliza o mesmo
                } else {
                    // senão, cria um novo se as credenciais estiverem corretas
                    if (! $token = JWTAuth::attempt($credentials)) {
                        return response()->json(['error' => 'Credenciais inválidas'], Response::HTTP_UNAUTHORIZED);   // 401
                    }
                }

                // PASSO 4 - Cria a instância do token e pega o payload no formato de array
                $payload = JWTAuth::setToken($token)->getPayload();
                // dd($payload);

                // Recalcula e renova a data de expiração - o Model esta fazendo isso

                /**
                 * PASSO 5 - Persiste o Token
                 *  Cria um novo token no banco de dados ou atualiza se já existir se for o mesmo (user_id, Token e active)
                 *  permitir que um usuário tenha vários tokens ativos (multi-dispositivos), mas sem repetição do mesmo token
                 *  No User Model, getJWTCustomClaims() já monta o payload com as informações do usuário
                 */
                Token::updateOrCreate(
                    // ['token' => $token], // ← Busca APENAS pelo token (é único) = token. The token has been blacklisted
                    ['user_id' => $payload['user_id'], 'token' => $token, 'status' => 'active'], // Verifica user_id + token
                    [
                        'expires_at' => $payload['exp'],                // quando expira o token
                        'updated_at' => $payload['iat'],                // data de criação/atualização do token
                        'status' => 'active',                           // status do token
                        'ip' => $request->ip(),                         // IP do usuário
                        'browser' => $request->header('User-Agent'),    // User-Agent (browser) do usuário
                    ]
                );                       

                return response()->json(compact('token'));
            });

        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível criar o token. ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);   // 500
        }
    }    

    public function selectSystem(Request $request)
    {
        try {
            // verifica se recebeu o SystemId no request
            $systemId = $request->input('systemId');
            if (! $systemId) {
                return response()->json(['error' => 'Informar o SystemId é obrigatório.'], Response::HTTP_UNAUTHORIZED);   // 401
            }

            // Verifica se o token foi enviado no cabeçalho Authorization
            if (! $token = $request->bearerToken()) {
                return response()->json(['error' => 'Token não fornecido'], Response::HTTP_UNAUTHORIZED);
            }            

            // Valida e obtém o usuário autenticado
            $user = JWTAuth::parseToken()->authenticate();
            // if (! JWTAuth::parseToken()->authenticate()) {
            if (! $user) {
                return response()->json(['error' => 'Usuário não encontrado'], Response::HTTP_UNAUTHORIZED);
            }            

            // Recupera o token do header Authorization
            $token = JWTAuth::getToken();

            // Decodificar o payload para obter o ID do usuário
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload['sub']; // ID do usuário no token

            // Verifica na tabela `acl_tokens` se o token ainda está ativo
            $tokenExists = Token::where('token', $token)    // Comparação direta com o token
                // ->where('user_id', $userId)                 // pelo User ID não dá porque um User pode ter mais de um Token
                ->where('status', 'active')                 // Apenas tokens ativos são válidos
                ->exists();     
                
            // Se o token foi revogado ou não encontrado, retorna erro 401
            if (! $tokenExists) {
                return response()->json(['error' => 'Token revogado ou expirado'], Response::HTTP_UNAUTHORIZED);
            }    
            
            /**
             * Ao fazer o refresh todo o token é remontado, ou seja, o payload é recriado
             * Ou seja, Recalcula e renova a data de expiração e inclui as abilities
             */
            // Gera novo token com claims automáticas
            $newToken = JWTAuth::fromUser($user);
            return response()->json(compact('newToken'));

            // retorna o payload igual ao login que contém todas informações do token JWT recebido no cabeçalho
            // return response()->json($payload);


        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido. ' . $e->getMessage()], Response::HTTP_UNAUTHORIZED);   // 401
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Erro inesperado',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }       
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {    
            return DB::transaction(function () use ($request) {

                // STEP 1 - Lest's insert the User
                $user = User::create([
                    'name' => trim($request->name),
                    'email' => trim(strtolower($request->email)),
                    'password' => Hash::make(trim($request->password)),     // Hash seguro
                    'active' => 'N',
                ]); 

                // Caso veio o $systemId, vamos associar o usuário a um sistema específico
                // $systemId = $request->input('systemId');
                if ($request->system_id) {
                    // Lógica para associar o usuário ao sistema
                    /**
                     * operacao	"assignSystem"
                     * user_id	"7"
                     * system_id	"2"
                     */

                    // assign de System (attach)
                    $user->systems()->attach($request->system_id);
                }

                // STEP 2 - Lest's generate token JWT to created User
                $token = JWTAuth::fromUser($user);
                $expiresAt = now()->addMinutes(config('jwt.ttl', 60));      // Expira conforme config

                // STEP 3 - Lest's insert the token on table
                Token::create([
                    'user_id' => $user->id,
                    'token' => $token,
                    'expires_at' => $expiresAt,
                    'status' => 'active',
                    'ip' => $request->ip(),
                    'browser' => $request->header('User-Agent'),
                ]);

                // Notifica o Usuário da operação
                $system = System::findOrFail($request->system_id);
                $systemName = $system->name;

                Mail::to($user->email)->send(new NotifyUserMail(
                    "Registro de Usuário.", 
                    $user->name, 
                    "Seu pedido de Registro no sistema {$systemName} foi executado com sucesso.\n" . 
                    "Aguarde o Administrador analisar e lhe conceder o Perfil de Acesso necessário.\n" .
                    "Então, um novo E-mail lhe será enviado.",
                ));                

                return response()->json([
                    'message' => 'Usuário registrado com sucesso!',
                    'user' => $user,
                    'token' => $token,
                    'expires_at' => $expiresAt,
                ], Response::HTTP_CREATED);          
            });
  
        } catch (Exception $e) {
            return response()->json(['error' => 'Houve um erro ao processar a operação. ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);   // 500
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            // Validação
            $request->validate([
                'email' => 'required|string|email|max:255',
            ]);
    
            // Verifica se o usuário existe
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'Usuário não encontrado.'], Response::HTTP_NOT_FOUND); // 404
            }
    
            // Gera token seguro
            $token = Str::random(64);
    
            // Salva ou atualiza token na tabela
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now(),
                ]
            );

            // Acesse o domínio configurado no .env
            $frontendUrl = env('FRONTEND_URL');
    
            // Monta URL de redefinição
            // $resetUrl = url("/resetPassword/{$token}?email=" . urlencode($user->email));
            // Monta a URL de redefinição com o token e o email
            $resetUrl = url("{$frontendUrl}/#/pages/resetpassword/?token={$token}&email=" . urlencode($user->email));
    
            // Tenta enviar o e-mail
            Mail::to($user->email)->send(new ForgotPasswordMail($user->name, $resetUrl));
    
            return response()->json(['message' => 'Um e-mail foi enviado para você com instruções para a redefinição da senha.'], Response::HTTP_OK); // 200
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
    
        } catch (Exception $e) {
            // Loga o erro detalhadamente
            Log::error('Erro ao processar forgotPassword: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
    
            return response()->json([
                'message' => 'Erro interno ao tentar enviar e-mail de redefinição. Tente novamente mais tarde.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // 500
        }    
    }    

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Token não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        if (!Hash::check($request->token, $record->token)) {
            return response()->json(['message' => 'Token inválido ou expirado.'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        try {
            DB::transaction(function () use ($user, $request) {
                // Atualiza a senha
                $user->password = Hash::make($request->password);
                $user->save();

                // Remove o token de reset
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();

                // Notifica por e-mail
                Mail::to($user->email)->send(new NotifyUserMail(
                    "Alteração de Senha", 
                    $user->name, 
                    "Sua senha foi alterada com sucesso.",
                ));
            });

            return response()->json(['message' => 'Senha redefinida com sucesso.'], Response::HTTP_OK);

        } catch (Exception $e) {
            // Logue o erro se necessário
            // Log::error('Erro ao redefinir senha', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Erro ao redefinir a senha. Tente novamente mais tarde.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    } 

    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            
            // Tenta decodificar mesmo que o token esteja expirado
            JWTAuth::setToken($token)->checkOrFail();
            
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload->get('sub');

            JWTAuth::invalidate($token);
            Auth::logout();

            Token::where('user_id', $userId)
                ->where('token', $token)
                ->update(['status' => 'invalidated']);

            return response()->json(['message' => 'Logout realizado com sucesso'], Response::HTTP_OK);
            
        } catch (TokenExpiredException $e) {
            // Token expirado, mas ainda assim podemos invalidá-lo na base de dados
            $token = JWTAuth::getToken();
            $payload = JWTAuth::manager()->getJWTProvider()->decode($token);
            $userId = $payload['sub'];
            
            Token::where('user_id', $userId)
                ->where('token', $token)
                ->update(['status' => 'invalidated']);

            return response()->json(['message' => 'Sessão expirada'], Response::HTTP_ACCEPTED);
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_OK);
        }
    }    
    
    public function me(Request $request)
    {
        try {
            // Verifica se o token foi enviado no cabeçalho Authorization
            if (! $token = $request->bearerToken()) {
                return response()->json(['error' => 'Token não fornecido'], Response::HTTP_UNAUTHORIZED);
            }            

            // Valida e obtém o usuário autenticado
            if (! JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Usuário não encontrado'], Response::HTTP_UNAUTHORIZED);
            }            

            // Recupera o token do header Authorization
            $token = JWTAuth::getToken();

            // Decodificar o payload para extrair o ID do usuário
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload['sub']; // ID do usuário no token

            // Verifica na tabela `acl_tokens` se o token ainda está ativo
            $tokenExists = Token::where('token', $token)        // Comparação direta com o token
                ->where('user_id', $userId)  // pelo User ID não dá porque um User pode ter mais de um Token
                ->where('status', 'active')     // Apenas tokens ativos são válidos
                ->exists();     
                
            // Se o token foi revogado ou não encontrado, retorna erro 401
            if (! $tokenExists) {
                return response()->json(['error' => 'Token revogado ou expirado'], Response::HTTP_UNAUTHORIZED);
            }                

            // retorna o payload igual ao login que contém todas informações do token JWT recebido no cabeçalho
            return response()->json($payload);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido. ' . $e->getMessage()], Response::HTTP_UNAUTHORIZED);   // 401
        }
    }

    public function isTokenValid(Request $request)
    {
        try {
            // Verifica se o token foi enviado no cabeçalho Authorization
            if (! $token = $request->bearerToken()) {
                return response()->json(['error' => 'Token não fornecido'], Response::HTTP_UNAUTHORIZED);
            }            

            // Valida e obtém o usuário autenticado
            if (! JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Usuário não encontrado'], Response::HTTP_UNAUTHORIZED);
            }            

            // Recupera o token do header Authorization
            $token = JWTAuth::getToken();

            // Decodificar o payload para obter o ID do usuário
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload['sub']; // ID do usuário no token

            // Verifica na tabela `acl_tokens` se o token ainda está ativo
            $tokenExists = Token::where('token', $token)    // Comparação direta com o token
                ->where('user_id', $userId)                 // pelo User ID não dá porque um User pode ter mais de um Token
                ->where('status', 'active')                 // Apenas tokens ativos são válidos
                ->exists();     
                
            // Se o token foi revogado ou não encontrado, retorna erro 401
            if (! $tokenExists) {
                return response()->json(['error' => 'Token revogado ou expirado'], Response::HTTP_UNAUTHORIZED);
            }                

            // retorna status = ok
            return response()->json(['status' => 'ok']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido. ' . $e->getMessage()], Response::HTTP_UNAUTHORIZED);   // 401
        }
    }       

    public function refreshOLD(Request $request)
    {
        // TODO terminar, perguntar a lógica ao chat GPT sobre refresh_token
        // Verifica se o refresh token foi enviado
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->json(['error' => 'Refresh token não fornecido.'], Response::HTTP_BAD_REQUEST);   // 400
        }

        try {
            // Tente obter o novo token usando o refresh token
            $newToken = JWTAuth::refresh($refreshToken);  // Isso vai gerar um novo token JWT

            // Se necessário, você pode validar se o refresh token é válido
            // Exemplo: $refreshTokenData = RefreshToken::where('token', $refreshToken)->first();

            return response()->json([
                'message' => 'Token renovado com sucesso!',
                'token' => $newToken,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Refresh token inválido ou expirado' . $e->getMessage()], Response::HTTP_UNAUTHORIZED);   // 401
        }
    }

public function refresh(Request $request)
{
    try {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token não fornecido.'], 400);
        }

        // 🔧 1. Tenta autenticar o usuário (igual ao selectSystem)
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // 🔧 2. Se o token expirou, extrai o usuário do payload
            $payload = JWTAuth::setToken($token)->getPayload();
            $user = User::find($payload->get('sub'));
        }

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        // 🔧 3. Renova o token usando JWTAuth::refresh (que mantém as claims)
        try {
            $newToken = JWTAuth::refresh($token);
        } catch (JWTException $e) {
            // Se o refresh falhar, gera um novo token do usuário
            $newToken = JWTAuth::fromUser($user);
        }

        $payload = JWTAuth::setToken($newToken)->getPayload();

        // 4. Persiste na tabela
        Token::updateOrCreate(
            ['user_id' => $payload['user_id'], 'token' => $newToken, 'status' => 'active'],
            [
                'expires_at' => $payload['exp'],
                'updated_at' => $payload['iat'],
                'status' => 'active',
                'ip' => $request->ip(),
                'browser' => $request->header('User-Agent'),
            ]
        );

        return response()->json([
            'message' => 'Token renovado com sucesso!',
            'token' => $newToken,
        ]);

    } catch (TokenInvalidException $e) {
        return response()->json(['error' => 'Token inválido.'], 401);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Token inválido ou expirado'], 401);
    } catch (Exception $e) {
        return response()->json(['error' => 'Erro ao renovar token'], 401);
    }
}

    public function forceRefresh(Request $request)
    {
        // Verifica se o usuário autenticado é um admin
        // $admin = auth()->user();
        // if (!$admin || !$admin->hasRole('Administrator')) {
        //     return response()->json(['error' => 'Acesso negado'], 403);
        // }

        // TODO 
        // não pode renovar um token expirado ou revogado, somente ativo
        // Exibir mensgem no titulo da card table igual ao botaõ revoke
    
        // Obtém o ID do token a ser atualizado
        $tokenId = $request->input('tokenId');
    
        // Busca o token na tabela
        $tokenEntry = Token::find($tokenId);
    
        if (! $tokenEntry) {
            return response()->json(['error' => 'Token não encontrado'], Response::HTTP_NOT_FOUND);   // 404
        }
    
        // Verifica se o token está expirado
        if (Carbon::parse($tokenEntry->expires_at)->isPast()) {
            return response()->json(['error' => 'Token expirado, faça login novamente'], Response::HTTP_NOT_FOUND);   // 401
        }
    
        // Obtém o usuário associado ao token
        $user = User::find($tokenEntry->user_id);
        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado'], Response::HTTP_NOT_FOUND);   // 404
        }
    
        // Gera um novo token para o usuário
        $newToken = JWTAuth::fromUser($user);
    
        // Atualiza a entrada na tabela de tokens
        $tokenEntry->update([
            'token' => $newToken,
            'expires_at' => now()->addMinutes(config('jwt.ttl', 60)),
            'updated_at' => now(),
        ]);
    
        return response()->json([
            'message' => 'Token atualizado com sucesso!',
            'error' => 'Token atualizado com sucesso!',
            'new_token' => $newToken,
        ]);
    }

    public function listTokens()
    {
        // Verificar se o usuário autenticado é admin (pode ser um middleware ou outra lógica de permissão)
        // $user = JWTAuth::parseToken()->authenticate();
        // if (!$user || !$user->hasRole('admin')) {
        //     return response()->json(['error' => 'Acesso negado'], 403);
        // }

        // Recuperar todos os tokens e seus status e o respectivo usuário
        // $tokens = Token::with('user')->orderBy('created_at','desc')->get();
        $tokens = Token::with('user')->latest('created_at')->get();
        $tokensResource = TokenResource::collection($tokens);   // Aplica um recurso para a coleção de tokens

        return response()->json($tokensResource);
    }    

    public function revoke(Request $request)
    {
        // TODO testar tudo
        // Verificar se o usuário autenticado é admin
        // $user = JWTAuth::parseToken()->authenticate();
        // if (!$user || !$user->hasRole('admin')) {
        //     return response()->json(['error' => 'Acesso negado. '], Response::HTTP_FORBIDDEN);   // 403
        // }

        // Encontrar o token pelo ID
        $token = Token::find($request->tokenId);

        if (!$token) {
            return response()->json(['error' => 'Token não encontrado'], Response::HTTP_NOT_FOUND);   // 404
        }

        // Alterar o status do token para "expired"
        $token->status = 'revoked';
        $token->save();

        return response()->json(['message' => 'Token Revogado com sucesso.'], Response::HTTP_OK);   // 200
    }    
    
    private function sendWhatsAppMessage($phone, $message)
    {
        $apiToken = env('ZAPI_TOKEN');
        $instanceId = env('ZAPI_INSTANCE_ID');
    
        try {
            $response = Http::post("https://api.z-api.io/instances/{$instanceId}/token/{$apiToken}/send-messages", [
                'phone' => $phone,
                'message' => $message,
            ]);
    
            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Erro ao enviar WhatsApp: ' . $response->body());
                return false;
            }
        } catch (Exception $e) {
            Log::error('Exceção ao enviar WhatsApp: ' . $e->getMessage());

            return response()->json([
                'error' => 'Exceção ao tentar enviar mensagem via WhatsApp.',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);   // 500          
        }
    }

    public function loginWithoutBD(Request $request)
    {
        // Recupera as credenciais do request
        $credentials = $request->only('email', 'password');

        // Tenta realizar a autenticação: 'attempt' GERA UM NOVO TOKEN
        try {
            // Se as credenciais estiverem incorretas
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciais inválidas'], Response::HTTP_UNAUTHORIZED);   // 401
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível criar o token. ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);   // 500
        }

        return response()->json(compact('token'));
    }

    public function update(Request $request)
    {
        try {        
            // Verifica se o token foi enviado
            if (!$token = $request->bearerToken()) {
                return response()->json(['error' => 'Token não fornecido'], Response::HTTP_UNAUTHORIZED);
            }            

            // Valida e obtém o usuário autenticado
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], Response::HTTP_UNAUTHORIZED);
            }

            // Validação dos campos
            $validated = $request->validate([
                'name' => 'required|string|min:6|max:50',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'string|min:10|max:15|nullable',
            ]);

            // Aplica os campos validados no modelo
            $user->fill($validated);
            $user->save();

            return response()->json($user);
            
        } catch (ValidationException $e) {
            // Captura específica para erros de validação
            return response()->json([
                'message' => 'Dados de entrada inválidos',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
            
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Houve um erro ao salvar os dados do usuário. ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    

    public function updatePhoto(Request $request)
    {
        try {        
            // Verifica se o token foi enviado no cabeçalho Authorization
            if (!$token = $request->bearerToken()) {
                return response()->json(['error' => 'Token não fornecido'], Response::HTTP_UNAUTHORIZED);
            }            

            // Valida e obtém o usuário autenticado
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], Response::HTTP_UNAUTHORIZED);
            }

            // Valida o arquivo de imagem
            $request->validate([
                'photo' => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'removerFoto' => 'sometimes|boolean',       // se presente deve ser um bool
            ]);

            // Verifica se recebeu uma nova em um (arquivo real), processa o upload
            if ($request->hasFile('photo')) {

                // 1. armazena o caminho e nome da foto antiga
                $caminhoFotoAntiga = $user->photo;

                // 2. Armazena o novo arquivo temporariamente
                $caminhoFotoNova = $request->file('photo')->store('users', 'public');

                DB::transaction(function () use ($user, $caminhoFotoAntiga, $caminhoFotoNova) {
                    // Atualiza o caminho da nova foto no banco
                    $user->photo = $caminhoFotoNova;
                    $user->save();

                    // Deleta a foto antiga (apenas se tudo acima foi bem-sucedido)
                    if ($caminhoFotoAntiga && Storage::disk('public')->exists($caminhoFotoAntiga)) {
                        Storage::disk('public')->delete($caminhoFotoAntiga);
                    }
                });
                
                return response()->json([
                    'message' => 'Foto atualizada com sucesso',
                    'user' => $user
                ], Response::HTTP_OK);
            }

            // verifica se é para excluir a foto. está removerFoto deve estar true
            if ($request->boolean('removerFoto')) {

                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                    $user->photo = null;
                    $user->save();
                }

                return response()->json([
                    'message' => 'Foto do Usuário excluída com sucesso',
                    'user' => $user
                ], Response::HTTP_OK);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dados de entrada inválidos',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
            
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Houve um erro ao processar a operação. ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    
    
    public function changePassword(Request $request)
    {
         try {        
            // Verifica se o token foi enviado no cabeçalho Authorization
            if (!$token = $request->bearerToken()) {
                return response()->json(['error' => 'Token não fornecido'], Response::HTTP_UNAUTHORIZED);
            }            

            // Valida e obtém o usuário autenticado
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], Response::HTTP_UNAUTHORIZED);
            }

            // Validando os campos
            // $validated = $request->validate([
            $validated = $request->validate([
                'senhaAtual' => 'required|string',
                'novaSenha' => 'required|string|min:6|confirmed|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
            ]);

            // Verificar se a senha atual fornecida é a mesma que a senha armazenada
            if (!Hash::check($validated['senhaAtual'], $user->password)) {
                return response()->json(['error' => 'A Senha Atual não está correta.'], Response::HTTP_UNAUTHORIZED);
            }     
            
            // Criptografando e alterando a nova senha
            $user->password = Hash::make($validated['novaSenha']);
            $user->save();

            /**
             * Um User pode ter acesso a vários Sistema com a mesma senha (Login Centralizado)
             * Porém ele pede changePassword() a partir de um Sistema, então pegamos o nome deste 
             */
            // Decodificar o payload para extrair o ID do System
            // Recupera o token do header Authorization
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token);
            $systemId = $payload->get('system_id');            

            // vamos obter o nome do System
            $system = System::findOrFail($systemId);
            $systemName = $system->name;

            // Notifica o Usuário da operação
            Mail::to($user->email)->send(new NotifyUserMail(
                "Alteração de Senha.", 
                $user->name, 
                "A operação que você solicitou a partir do {$systemName} para alterar sua senha de acesso foi executada com sucesso.",
            ));

            return response()->json(['message' => 'Senha alterada com sucesso.'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => 'Houve um erro ao processar a operação. ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);   // 500
        }
    }
    
}
