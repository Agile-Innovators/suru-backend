<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoints Authentication Module
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

//Endpoints Users Module
Route::put('/user/update/{id}', [UserController::class, 'update']);
Route::post('/user/{id}/update-password', [UserController::class, 'updatePassword']);
Route::post('/user/reset-password', [UserController::class, 'resetPassword']);
Route::post('/user/update/operational-hours/{id}', [UserController::class, 'updateOperationalHours']);

// Endpoints Properties Module
Route::get('/properties', [PropertyController::class, 'index']);
Route::post('/properties',[PropertyController::class, 'store']);
Route::delete('/properties/delete/{id}',[PropertyController::class, 'destroy']);
Route::get('/properties/property/{id}', [PropertyController::class, 'show']);
Route::put('/properties/update/{id}', [PropertyController::class, 'update']);
Route::get('/properties/user/{id}', [PropertyController::class, 'getUserProperties']);

// Endpoints Locations Module
Route::get('/locations', [LocationController::class, 'getAllLocations']);

// Endpoints Partners Module