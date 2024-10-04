<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\AppointmentController;

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
Route::get('/properties/filter', [PropertyController::class, 'filterProperty']);

// Endpoints Locations Module
Route::get('/locations', [LocationController::class, 'getAllLocations']);
Route::post('/locations/associate-user', [LocationController::class, 'associateUserWithLocation']);

// Endpoints Partners Module
Route::get('/partners-categories', [PartnersController::class, 'getPartnersCategories']);
Route::get('/partners', [PartnersController::class, 'getAllPartners']);
Route::get('/partners/{category}', [PartnersController::class, 'getPartnersByCategory']);
Route::get('/partner/{user_id}', [PartnersController::class, 'getPartnerById']);
Route::get('/partner-services/{user_id}', [PartnersController::class, 'getPartnerServices']);
Route::post('/partner-update-services/{user_id}', [PartnersController::class, 'updatePartnerServices']);
Route::post('/add-business-service', [PartnersController::class, 'addBusinessService']);

// Endpoints Appointments Module
Route::get('/appointments', [AppointmentController::class, 'index']);
Route::post('/appointment', [AppointmentController::class, 'store']);
Route::get('/appointment/{appointment_id}', [AppointmentController::class, 'show']);
Route::put('/appointment/{appointment_id}', [AppointmentController::class, 'update']);
Route::delete('/appointment/{appointment_id}', [AppointmentController::class, 'destroy']);
Route::get('/appointments/user/{user_id}', [AppointmentController::class, 'userAppointments']);
Route::get('/appointments/property/{property_id}', [AppointmentController::class, 'propertyAppointments']);
Route::get('/appointments/user/{user_id}/status/{status}', [AppointmentController::class, 'getUserAppointmentsByStatus']);
Route::get('/appointments/property/{property_id}/status/{status}', [AppointmentController::class, 'getPropertyAppointmentsByStatus']);