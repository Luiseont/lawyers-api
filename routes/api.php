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
        Route::get('hola', function(){
            return 'hola';
        });
    });

    //rutas para administradores
    Route::middleware('adminAccess')->group(function(){
        Route::get('holaAdmin', function(){
            return 'hola';
        });
    });


});
