<?php

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

Route::post('login', [App\Http\Controllers\API\AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

    //rutas para abogados
    Route::middleware('lawyerAccess')->group(function(){

        Route::post('suscription', [App\Http\Controllers\API\LawyersController::class, 'store']);
        Route::get('suscription/{id}', [App\Http\Controllers\API\LawyersController::class, 'show']);
        Route::put('suscription', [App\Http\Controllers\API\LawyersController::class, 'update']);
        Route::delete('suscription/{id}', [App\Http\Controllers\API\LawyersController::class, 'destroy']);

    });

    //rutas para administradores
    Route::middleware('adminAccess')->group(function(){
        Route::get('holaAdmin', function(){
            return 'hola';
        });
    });


});
