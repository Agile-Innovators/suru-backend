<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Endpoints Authentication Module
Route::post('/register/user', [UserController::class, 'registerUser']);
Route::post('/register/partner', [UserController::class, 'registerPartner']);
Route::post('/login', [UserController::class, 'login']);

// Endpoints Users Module


// Endpoints Properties Module


// Endpoints Partners Module