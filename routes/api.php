<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// JWT Tokens
Route::post('auth/me',            [AuthController::class, 'me']);
Route::post('auth/login',         [AuthController::class, 'login']);
Route::post('auth/loginWithoutBD',[AuthController::class, 'loginWithoutBD']);
Route::post('auth/selectSystem',  [AuthController::class, 'selectSystem']);
Route::post('auth/forgotpassword',[AuthController::class, 'forgotpassword']);
Route::post('auth/resetPassword', [AuthController::class, 'resetPassword']);
Route::post('auth/logout',        [AuthController::class, 'logout']);
Route::post('auth/refresh',       [AuthController::class, 'refresh']);
Route::post('auth/register',      [AuthController::class, 'register']);
Route::post('auth/revoke',        [AuthController::class, 'revoke']);
Route::post('auth/forceRefresh',  [AuthController::class, 'forceRefresh']);
Route::get( 'auth/listTokens',    [AuthController::class, 'listTokens']);
Route::get( 'auth/isTokenValid',  [AuthController::class, 'isTokenValid']);

// Nova rota para atualizar a foto do usuário, ainda não segura
Route::post('user/update', [AuthController::class, 'update'])->name('user.update');
Route::post('user/updatephoto', [AuthController::class, 'updatePhoto'])->name('user.updatePhoto');



// JWT
Route::GET('/test', function (Request $request) {
    return response()->json(['token' => 'ACL 4 - Guard API Response Ok']);
});

// Rota Protegida (para testar o token)
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return response()->json(['user' => $request->user()]);
// });


// Route::post('/auth/login', function (Request $request) {
//     $credentials = $request->only('email', 'password');

//     if (!$token = JWTAuth::attempt($credentials)) {
//         return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
//     }

//     // try {
//     //     $token = JWTAuth::attempt($credentials);
//     // } catch (Exception $e) {
//     //     return response()->json(['error' => 'Could not create token.' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);   // 500
//     // }

//     return response()->json(['token' => $token])->header('Content-Type', 'application/json; charset=utf-8');
// });

// Route::get('/auth/me', function (Request $request) {
//     try {
//         $user = JWTAuth::parseToken()->authenticate();
//         return response()->json($user);
//     } catch (Exception $e) {
//         return response()->json(['error' => 'Token inválido'], 401);
//     }
// })->middleware('auth:api');

