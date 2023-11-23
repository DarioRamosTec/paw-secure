<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::controller(UsersController::class)->prefix('v1')->group(function () {
    Route::post('/register', 'store');
    Route::get('/activate/{email}', 'activate')
    ->name('activating')->middleware('signed');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('me', [AuthController::class, 'me']);

    Route::get('user/{id}', [UsersController::class, 'index'])->where('id', '[0-9]+')->middleware(['check.role']);
    Route::put('user/{id}', [UsersController::class, 'update'])->where('id', '[0-9]+')->middleware(['check.role']);
    Route::delete('user/{id}', [UsersController::class, 'destroy'])->where('id', '[0-9]+')->middleware(['check.role']);
});
