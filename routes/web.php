<?php

use App\Http\Controllers\MiscController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controller(UsersController::class)->prefix('v1')->group(function () {
    Route::get('/activate/{email}', 'activate')
    ->name('activating')->middleware('signed');
});

Route::group([], function () {
    Route::get('/me/pets/{id}', [UsersController::class, 'mypets'])->middleware('auth');
});