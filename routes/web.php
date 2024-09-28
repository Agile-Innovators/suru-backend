<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Mail\Welcome;
use Resend\Laravel\Facades\Resend;

Route::get('/', function () {
    return view('layout');
});

// Main controllers resources routes
Route::resource('users', UserController::class);

Route::get('/email', function () {
    // return new Welcome('Kevin');

    Resend::emails()->send([
        'from' => env('MAIL_FROM_NAME'). ' <' . env('MAIL_FROM_ADDRESS') . '>',
        'to' => 'correo@gmail.com',
        'subject' => 'Testing Resend with Laravel 3',
        'html' => (new Welcome('kevin'))->render(),
    ]);
    
});
