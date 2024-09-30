<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;

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
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoints Authentication Module
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

//Endpoints Users Module
Route::put('/user/{id}', [UserController::class, 'update']);
Route::post('/user/{id}/update-password', [UserController::class, 'updatePassword']);
Route::post('/user/reset-password', [UserController::class, 'resetPassword']);

// Endpoints Properties Module
Route::get('/properties', [PropertyController::class, 'index']);
Route::post('/properties',[PropertyController::class, 'store']);
Route::delete('/properties/delete/{id}',[PropertyController::class, 'destroy']);
Route::get('/properties/property/{id}', [PropertyController::class, 'show']);
Route::put('/properties/update/{id}', [PropertyController::class, 'update']);

// Endpoints Partners Module