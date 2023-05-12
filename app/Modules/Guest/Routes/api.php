<?php

use App\Modules\Guest\Http\Controllers\GuestController;

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
    'prefix' => 'guest'
], function ($router) {
    Route::middleware('permission:list_guest')->get('/list', [GuestController::class, 'show']);
    Route::middleware('permission:detail_guest')->get('/detail/{id}', [GuestController::class, 'index']);
    Route::middleware('permission:create_guest')->post('/create', [GuestController::class, 'create']);
    Route::middleware('permission:update_guest')->put('/update/{id}', [GuestController::class, 'update']);
    Route::middleware('permission:delete_guest')->delete('/delete/{id}', [GuestController::class, 'destroy']);
});
