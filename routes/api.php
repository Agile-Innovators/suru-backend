<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoints Authentication Module
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

//Endpoints Users Module
Route::put('/user/{id}', [UserController::class, 'update']);

// Endpoints Properties Module


// Endpoints Partners Module