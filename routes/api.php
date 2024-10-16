<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PropertyCategoryController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyTransactionTypeController;
use App\Http\Controllers\FavoritesController;

// Endpoints Authentication Module
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth.jwt']], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    //Endpoints Users Module
    Route::put('/user/update/{id}', [UserController::class, 'update']);
    Route::post('/user/{id}/update-password', [UserController::class, 'updatePassword']);
    Route::post('/user/reset-password', [UserController::class, 'resetPassword']);
    Route::post('/user/update/operational-hours/{id}', [UserController::class, 'updateOperationalHours']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::get('/user/operational-hours/{id}', [UserController::class, 'showOperationalHours']);

    // Endpoints Properties Module
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::delete('/properties/delete/{id}', [PropertyController::class, 'destroy']);
    Route::put('/properties/update/{id}', [PropertyController::class, 'update']);
    Route::get('/properties/user/{id}', [PropertyController::class, 'getUserProperties']);

    // Endpoints Locations Module
    Route::post('/locations/associate-user', [LocationController::class, 'associateUserWithLocation']);

    // Endpoints Partners Module
    Route::get('/partner-services/{user_id}', [PartnersController::class, 'getPartnerServices']);
    Route::post('/partner-update-services/{user_id}', [PartnersController::class, 'updatePartnerServices']);
    Route::post('/add-business-service', [PartnersController::class, 'addBusinessService']);

    // Endpoints Appointments Module
    Route::post('/appointment', [AppointmentController::class, 'store']);
    Route::get('/appointment/{appointment_id}', [AppointmentController::class, 'show']);
    Route::put('/appointment/{appointment_id}', [AppointmentController::class, 'update']);
    Route::delete('/appointment/{appointment_id}', [AppointmentController::class, 'destroy']);
    Route::get('/appointments/user/{user_id}', [AppointmentController::class, 'userAppointments']);
    Route::get('/appointments/property/{property_id}', [AppointmentController::class, 'propertyAppointments']);
    Route::get('/appointments/user/{user_id}/status/{status}', [AppointmentController::class, 'getUserAppointmentsByStatus']);
    Route::get('/appointments/property/{property_id}/status/{status}', [AppointmentController::class, 'getPropertyAppointmentsByStatus']);
    Route::put('/appointment/cancel/{appointment_id}', [AppointmentController::class, 'cancelAppointment']);

    // Endpoints Favorites
    Route::get('/user/{user_id}/favorites', [FavoritesController::class, 'getFavoritesUser']);
    Route::get('/user/{user_id}/favorites/ids', [FavoritesController::class, 'getFavoritesUserIds']);
    Route::Post('/user/favorites/add', [FavoritesController::class, 'addFavoriteProperty']);
    Route::delete('/user/favorites/remove', [FavoritesController::class, 'removeFavoriteProperty']);
});
/**
 * Public routes: doesn't need authentication
 */

// Endpoints Properties Module
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/property/{id}', [PropertyController::class, 'show']);
Route::get('/properties/filter', [PropertyController::class, 'filterProperty']);

// Endpoints Locations Module
Route::get('/locations', [LocationController::class, 'getAllLocations']);

// Endpoints Partners Module
Route::get('/partners-categories', [PartnersController::class, 'getPartnersCategories']);
Route::get('/partners', [PartnersController::class, 'getAllPartners']);
Route::get('/partners/{category}', [PartnersController::class, 'getPartnersByCategory']);
Route::get('/partner/{user_id}', [PartnersController::class, 'getPartnerById']);

// Endpoints Utilities
Route::get('/utilities', [UtilityController::class, 'index']);

//Endpoints PropertyCategories
Route::get('/property-categories', [PropertyCategoryController::class, 'index']);

// Endpoints Regions
Route::get('/regions', [RegionController::class, 'index']);

// Endpoints PropertyTransactionTypes
Route::get('/property-transaction-types', [PropertyTransactionTypeController::class, 'index']);


