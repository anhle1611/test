<?php

use App\Modules\Auth\Http\Controllers\AuthenController;
use App\Modules\Auth\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthenController::class, 'login']);
    Route::post('/register', [AuthenController::class, 'register']);
    Route::post('/logout', [AuthenController::class, 'logout']);
    Route::post('/refresh', [AuthenController::class, 'refresh']);
    Route::get('/user-profile', [AuthenController::class, 'userProfile']);
    Route::post('/change-pass', [AuthenController::class, 'changePassWord']);
    Route::post('/change-role', [AuthenController::class, 'changeUserRole']);
    Route::get('/users', [UserController::class, 'listUsers']);
    Route::get('/roles', [UserController::class, 'listRoles']);
});
