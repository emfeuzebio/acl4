<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\TokenResource;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct()
    {
        // nesse caso ['login', 'register'] não dependem de estar autenticado
        // $this->middleware('auth:api', ['except' => ['login', 'register','loginTable','me','logout','revoke','listTokens','refresh','forceRefresh']]);

        // atualizar o status dos tokens expirados
        Token::where('expires_at', '<', now())->update(['status' => 'expired']);
    }

    // Login COM token persistente em banco de dados
    // TODO não está criando nova linha no BD para login do outro usuário, somente ao primeiro
    // precisa atualizar o updated_at e o expires_at toda ver que o mesmo user ficar login ou refresh
    public function loginTable(Request $request)
    {
            // Recupera as credenciais do request
            $credentials = $request->only('email', 'password');

            // Tenta realizar a autenticação
            try {
                // PASSO 1 - Autenticar usuário sem gerar token ainda
                $user = User::where('email', $credentials['email'])->first();

                if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                    return response()->json(['error' => 'Credenciais inválidas'], Response::HTTP_UNAUTHORIZED);   // 401
                }

                // PASSO 2 - Verifica se já existe um token ativo para o usuário
                $existingToken = Token::where('user_id', $user->id)
                    ->where('status', 'active')         // Verifica se está ativo
                    ->where('expires_at', '>', Carbon::now())   // Verifica se ainda está válido
                    ->first();

                // PASSO 3 - Caso exista, reutiliza o mesmo, SENÃO cria um novo
                if ($existingToken) {

                    // Se já existir um token válido, reutiliza o mesmo
                    $token = $existingToken->token;


                    // TODO antes de devolver o Token recuperado do BD,
                    // devemos verificar se o token RECEBIDO NO request está expirado
                    // e se o token do BD está expirado, se sim, devemos invalidar o token do BD

                        // Verifica o timestamp de expiração (exp) do token                                        
                        // $tokenRequest = JWTAuth::getToken();
                        // $payload = JWTAuth::parseToken()->getPayload();
                        // $payload = JWTAuth::setToken($tokenRequest)->getPayload();
                        // die($tokenRequest);
                        // die($existingToken);


                        // $exp = $payload->get('exp');
                        // $currentTime = Carbon::now()->timestamp;

                        // if ($exp < $currentTime) {
                        //     // Se a expiração for menor que o timestamp atual, o token expirou
                        //     return response()->json(['error' => 'Token expirado'], Response::HTTP_UNAUTHORIZED);   // 401
                        // }

                        // print_r($payload);
                        // die('token existente');
                } else {
                    // senão, cria um novo, mas se as credenciais estiverem incorretas, exception
                    if (! $token = JWTAuth::attempt($credentials)) {
                        return response()->json(['error' => 'Credenciais inválidas'], Response::HTTP_UNAUTHORIZED);   // 401
                    }
                }

                // Cria a instância do token e pega o payload no formato de array
                $payload = JWTAuth::setToken($token)->getPayload();

                // Só pega a data de expiração diretamente do payload e transforma em Timestamp
                // $expiresAt = Carbon::createFromTimestamp($payload['exp']);
                
                // recalcula e renova a data de expiração
                $expiresAt = Carbon::now()->addMinutes(config('jwt.ttl'));

                // Cria um novo token no banco de dados ou atualiza se já existir se for o mesmo (user_id, Token e active)
                // permitir que um usuário tenha vários tokens ativos (multi-dispositivos), mas sem repetição do mesmo token
                Token::updateOrCreate(
                    ['user_id' => $payload['user_id'], 'token' => $token, 'status' => 'active'], // Verifica user_id + token
                    [
                        'expires_at' => $expiresAt,
                        'updated_at' => Carbon::now(),
                        'status' => 'active',
                        'ip' => $request->ip(),            
                        'browser' => $request->header('User-Agent'),
                    ]
                );                

        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível criar o token. ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);   // 500
        }

        return response()->json(compact('token'));
    }    

    // Login sem banco de dados
    public function login(Request $request)
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

    // public function register(UserRegisterRequest $request): JsonResponse
    public function register(UserRegisterRequest $request)
    {
        // dd($request->all());

        // $validatedData = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:8|confirmed',
        //     // 'password_confirmation' => 'required|string|min:8',
        // ]);

        // die('registerX(Request $request)');
        $credentials = $request->only('name','email','password');

        // return response()->json($credentials, Response::HTTP_CREATED);        
        // print_r($credentials);


            // Valida os dados de entrada
            // $validatedData = $request->validate([
            //     'name' => 'required|string|min:6',
            //     'email' => 'required|string|email|max:255|unique:users,email',
            //     'password' => 'required|string|min:6|confirmed',
            // ]);
            
            // // Cria o usuário no banco de dados
            // $user = User::create([
            //     'name' => $validatedData['name'],
            //     'email' => $validatedData['email'],
            //     'password' => Hash::make($validatedData['password']), // Hash seguro
            // ]);
        // die('registerX(UserRequest $request)');

        // STEP 1 - Lest's insert the User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash seguro
            'active' => 'Y',
        ]); 
    
        // Gera o token JWT para o usuário recém-registrado
        $token = JWTAuth::fromUser($user);
        $expiresAt = now()->addMinutes(config('jwt.ttl', 60)); // Expira conforme config
    
        // Registra o token na tabela `acl_tokens`
        Token::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => $expiresAt,
            'status' => 'active',
            'ip' => $request->ip(),
            'browser' => $request->header('User-Agent'),
        ]);
    
        // Retorna a resposta JSON com o usuário e token
        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
            'user' => $user,
            'token' => $token,
            'expires_at' => $expiresAt,
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        try {
            // Recupera o token do header Authorization
            $token = JWTAuth::getToken();

            // Decodificar o payload para obter o ID do usuário
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload->get('sub'); // 'sub' geralmente é o ID do usuário

            // Atualizar o status do token na tabela para 'invalidated'
            Token::where('user_id', $userId)
                ->where('token', $token)
                ->update(['status' => 'invalidated']);

            // Invalida o token
            JWTAuth::invalidate($token);
            Auth::logout();

            return response()->json(['message' => 'Logout realizado com sucesso'], Response::HTTP_OK);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível realizar o logout. ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);   // 500
        }
    }  
    
    public function me(Request $request)
    {
        try {

            // Verifica se o token foi enviado no cabeçalho Authorization
            if (!$token = $request->bearerToken()) {
                return response()->json(['error' => 'Token não fornecido'], Response::HTTP_UNAUTHORIZED);
            }            

            // Recupera o usuário autenticado
            $user = JWTAuth::parseToken()->authenticate();
            // return response()->json($user);

            // Recupera o token do header Authorization
            $token = JWTAuth::getToken();

            // Decodificar o payload para obter o ID do usuário
            $payload = JWTAuth::setToken($token)->getPayload();

            // retorna o payload igual ao login que contém todas informações do token JWT recebido no cabeçalho
            return response()->json($payload);
        } catch (JWTException $e) {
            // Caso o token não seja válido ou ocorra outro erro
            return response()->json(['error' => 'Token inválido. ' . $e->getMessage()], Response::HTTP_UNAUTHORIZED);   // 401
        }
    }

    public function refresh(Request $request)
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Refresh token inválido ou expirado' . $e->getMessage()], Response::HTTP_UNAUTHORIZED);   // 401
        }
    }

    public function forceRefresh(Request $request)
    {
        // Verifica se o usuário autenticado é um admin
        // $admin = auth()->user();
        // if (!$admin || !$admin->hasRole('Administrator')) {
        //     return response()->json(['error' => 'Acesso negado'], 403);
        // }

        // TODO não pode renovar um token expirado ou revogado, somente ativo
    
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
            'expires_at' => now()->addMinutes(config('jwt.ttl')),
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
}
