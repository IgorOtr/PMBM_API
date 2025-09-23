<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChamadoController;
use App\Http\Controllers\RuralController;
use App\Http\Controllers\AnimalController;
use App\Models\User;

//Rotas de autenticação
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/users', [UserController::class, 'users']); //Rota apenas para teste

Route::middleware('auth:sanctum')->group(function () {

    //Rotas de usuário
    Route::get('/user', [UserController::class, 'me']);
    Route::post('/logout', [UserController::class, 'logout']);

    //Rodas de SMMU
    Route::get('/list/chamados', [ChamadoController::class, 'index']);
    Route::post('/create/chamados', [ChamadoController::class, 'store']); 

    //Rotas de SMDR
    Route::get('/list/rurals', [RuralController::class, 'index']);
    Route::post('/create/rurals', [RuralController::class, 'store']);

    //Rotas de SMPA
    Route::get('/list/denuncias', [AnimalController::class, 'listDenuncias']);
    Route::get('/list/castracoes', [AnimalController::class, 'listCastracoes']);
    Route::post('/create/denuncia', [AnimalController::class, 'storeDenuncia']);
    Route::post('/create/castracao', [AnimalController::class, 'storeCastracao']);

});