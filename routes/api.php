<?php

use App\Http\Controllers\Api\EmailTesteController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InviteApiController;

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminUserActionController;
use App\Http\Controllers\Admin\AdminUserProfileController;
use App\Http\Controllers\Admin\AdminUserQueryController;
use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Middleware\EnsureSystemAdmin;

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
Route::post('user/changepassword', [AuthController::class, 'changePassword'])->name('user.changePassword');


// Rota para microserviço para envio de email que será usado pelo DAS Guillon, ainda não segura
Route::get('/email/teste', [EmailTesteController::class, 'enviar']);


// JWT
Route::GET('/test', function (Request $request) {
    return response()->json(['token' => 'ACL 4 - Guard API Response Ok']);
});

Route::get ('auth/invite/{token}', [InviteApiController::class, 'show']);
Route::post('auth/invite/{token}', [InviteApiController::class, 'accept']);

Route::middleware(['auth:api', EnsureSystemAdmin::class])->prefix('admin')->group(function () {
    // usuários
    Route::get   ('users',                    [AdminUserController::class, 'index']);
    Route::post  ('users',                    [AdminUserController::class, 'store']);
    Route::get   ('users/{id}',               [AdminUserController::class, 'show']);
    Route::put   ('users/{id}',               [AdminUserController::class, 'update']);
    Route::delete('users/{id}',               [AdminUserController::class, 'destroy']);

        // // ações
        // Route::post('users/{id}/activate',        [AdminUserActionController::class, 'activate']);
        // Route::post('users/{id}/suspend',         [AdminUserActionController::class, 'suspend']);
        // Route::post('users/{id}/resend-invite',   [AdminUserActionController::class, 'resendInvite']);
        // Route::post('users/{id}/force-reset',     [AdminUserActionController::class, 'forceReset']);

        // // perfis
        // Route::get   ('users/{id}/profiles',              [AdminUserProfileController::class, 'index']);
        // Route::post  ('users/{id}/profiles/{profileId}',  [AdminUserProfileController::class, 'grant']);
        // Route::delete('users/{id}/profiles/{profileId}',  [AdminUserProfileController::class, 'revoke']);

        // // consultas
        // Route::get('users/{id}/authorizations',   [AdminUserQueryController::class, 'authorizations']);
        // Route::get('users/{id}/history',          [AdminUserQueryController::class, 'history']);

        // // auditoria global
        // Route::get('audit',                       [AdminAuditController::class, 'index']);
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

