<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\PetsController;
use App\Http\Controllers\SpacesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PetSpacesController;
use App\Http\Controllers\SensorsController;
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
    Route::post('register', 'store');
});

Route::group([
    'middleware' => 'locale',
    'prefix' => 'v1/auth'
], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    
    Route::post('me', [UsersController::class, 'update']);
    Route::get('lang', [UsersController::class, 'lang']);
    Route::put('lang/{lang}', [UsersController::class, 'lang']);
    Route::get('spaces', [UsersController::class, 'spaces']);
    Route::get('pets', [UsersController::class, 'pets']);

    Route::post('pet', [PetsController::class, 'store']);
    Route::put('pet/{id}', [PetsController::class, 'update'])->where('id', '[0-9]+');
    Route::get('pet/{id}', [PetsController::class, 'index'])->where('id', '[0-9]+');

    Route::get('space/{id}', [SpacesController::class, 'index'])->where('id', '[0-9]+');
    Route::post('space', [SpacesController::class, 'store']);
    Route::post('space/{id}/link', [SpacesController::class, 'link'])->where('id', '[0-9]+');
    Route::patch('space/{id}/target', [SpacesController::class, 'target'])->where('id', '[0-9]+');

    Route::post('spaces/{id}', [PetSpacesController::class, 'store'])->where('id', '[0-9]+');
    Route::put('spaces/{id}', [PetSpacesController::class, 'update'])->where('id', '[0-9]+');

    Route::get('space/{id}/position', [SensorsController::class, 'position'])->where('id', '[0-9]+');
    Route::get('space/{id}/motion', [SensorsController::class, 'motion'])->where('id', '[0-9]+');
    Route::get('space/{id}/sensor/{sensor}', [SensorsController::class, 'sensor'])->where('id', '[0-9]+');
    Route::get('space/{id}/sensors', [SensorsController::class, 'index'])->where('id', '[0-9]+');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('login');
});
