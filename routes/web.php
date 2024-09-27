<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('layout');
});

// Main controllers resources routes
Route::resource('users', UserController::class);