<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        // nesse caso ['login', 'register'] não dependem de estar autenticado
        $this->middleware('auth:api', ['except' => ['login', 'register','loginTable','me','logout','Revoke','listTokens']]);
    }

    // Login COM token persistente em banco de dados
    // TODO não está criando nova linha no BD para login do outro usuário, somente ao primeiro
    // precisa atualizar o updated_at e o expires_at toda ver que o mesmo user ficar login ou refresh
    // refresh não testado
    public function loginTable(Request $request)
    {
            // Recupera as credenciais do request
            $credentials = $request->only('email', 'password');

            // Tenta realizar a autenticação
            try {
                // PASSO 1 - Autenticar usuário sem gerar token ainda
                $user = User::where('email', $credentials['email'])->first();

                if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                    return response()->json(['error' => 'Credenciais inválidas'], 401);
                }

                // PASSO 2 - Verifica se já existe um token ativo para o usuário
                $existingToken = Token::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('expires_at', '>', now()) // Verifica se ainda está válido
                    ->first();

                // PASSO 3 - Caso exista, reutiliza o mesmo, SENÃO cria um novo
                if ($existingToken) {
                    // Se já existir um token válido, reutiliza o mesmo
                    $token = $existingToken->token;
                } else {
                    // senão, cria um novo, mas se as credenciais estiverem incorretas, exception
                    if (! $token = JWTAuth::attempt($credentials)) {
                        return response()->json(['error' => 'Credenciais inválidas'], Response::HTTP_UNAUTHORIZED);   // 401
                    }
                }

                // Cria a instância do token e pega o payload no formato de array
                $payload = JWTAuth::setToken($token)->getPayload();

                // Pega a data de expiração diretamente do payload
                $expiresAt = Carbon::createFromTimestamp($payload['exp']);             

                // Cria um novo token no banco de dados ou atualiza se já existir se for o mesmo user_id e token
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

        // Tenta realizar a autenticação
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
        // TODO payload deve ser igual ao login
        try {

            // Verifica se o token foi enviado no cabeçalho Authorization
            if (!$token = $request->bearerToken()) {
                return response()->json(['error' => 'Token não fornecido'], Response::HTTP_UNAUTHORIZED);
            }            

            // Recupera o usuário autenticado
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($user);
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

    public function listTokens()
    {
        // Verificar se o usuário autenticado é admin (pode ser um middleware ou outra lógica de permissão)
        // $user = JWTAuth::parseToken()->authenticate();
        // if (!$user || !$user->hasRole('admin')) {
        //     return response()->json(['error' => 'Acesso negado'], 403);
        // }
    
        // TODO testar tudo

        // Recuperar todos os tokens e seus status
        $tokens = Token::with('user')->get();

        return response()->json($tokens);
    }    

    public function Revoke($tokenId)
    {
        // TODO testar tudo
        // Verificar se o usuário autenticado é admin
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['error' => 'Acesso negado. '], Response::HTTP_FORBIDDEN);   // 403
        }

        // Encontrar o token pelo ID
        $token = Token::find($tokenId);

        if (!$token) {
            return response()->json(['error' => 'Token não encontrado'], Response::HTTP_NOT_FOUND);   // 404
        }

        // Alterar o status do token para "expired"
        $token->status = 'expired';
        $token->save();

        return response()->json(['message' => 'Token cancelado com sucesso.'], Response::HTTP_OK);   // 200
    }    
}
