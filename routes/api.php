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
        Route::get('getsuscriptions/{flag}', [App\Http\Controllers\API\AdminController::class, 'getSuscriptions']);
        Route::get('getsuscription/{id}', [App\Http\Controllers\API\AdminController::class, 'getSuscription']);
        Route::post('cancelSuscription', [App\Http\Controllers\API\AdminController::class, 'cancelSuscription']);
        Route::post('retryPayment', [App\Http\Controllers\API\AdminController::class, 'retryPaymentManual']);
    });


});
