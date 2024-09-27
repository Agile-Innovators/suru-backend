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
Route::post('/register/user', [UserController::class, 'registerUser']);
Route::post('/register/partner', [UserController::class, 'registerPartner']);
Route::post('/login', [UserController::class, 'login']);

// Endpoints Users Module


// Endpoints Properties Module
Route::get('/properties', [PropertyController::class, 'index']);
Route::post('/properties',[PropertyController::class, 'store']);

// Endpoints Partners Module