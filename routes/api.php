<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\VeiculoController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('CheckJWTToken')->group(function () {

    Route::apiResource('veiculo', VeiculoController::class);            // Funcionou Ok Sem validacão de abilities

    // Controla Autorizacões na Entidade Veiculo 
    Route::controller(VeiculoController::class)->group(function () {    
        // Route::get('veiculo',          'index')->name('veiculo.index');                          // SELECT * SEM validacão de abilities
        Route::get('veiculo',          'index')->middleware('CheckJWTAbilities:veiculo.index');     // SELECT * COM validacão de abilities baseado no nome da rota
        // Route::get('veiculo/{id}',      'show')->middleware('CheckJWTAbilities:veiculo.show');      // SELECT ID  
        // Route::put('veiculo/{id}',    'update')->middleware('CheckJWTAbilities:veiculo.update');    // UPDATE ID
        Route::post('veiculo',         'store')->middleware('CheckJWTAbilities:veiculo.store');     // INSERT       
        Route::delete('veiculo/{id}','destroy')->middleware('CheckJWTAbilities:veiculo.destroy');   // DELETE ID
    });

});

// JWT Tokens
Route::post('auth/me',          [AuthController::class, 'me']);
Route::post('auth/login',       [AuthController::class, 'login']);
Route::post('auth/loginTable',  [AuthController::class, 'loginTable']);
Route::post('auth/logout',      [AuthController::class, 'logout']);
Route::post('auth/refresh',     [AuthController::class, 'refresh']);
Route::post('auth/register',    [AuthController::class, 'register']);
Route::post('auth/revoke',      [AuthController::class, 'revoke']);
Route::post('auth/forceRefresh',[AuthController::class, 'forceRefresh']);
Route::get( 'auth/listTokens',  [AuthController::class, 'listTokens']);


// JWT
Route::GET('/test', function (Request $request) {

    return response()->json(['token' => 'ACL 4 - Guard API Response Ok']);
});

// // Rota Protegida (para testar o token)
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

