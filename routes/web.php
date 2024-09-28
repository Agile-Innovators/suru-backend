<?php

use App\Mail\Welcome;
use Illuminate\Support\Facades\Route;
use Resend\Laravel\Facades\Resend;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('layout');
});

Route::get('/email', function () {
    // return new Welcome('Kevin');

    Resend::emails()->send([
        'from' => env('MAIL_FROM_NAME'). ' <' . env('MAIL_FROM_ADDRESS') . '>',
        'to' => 'correo@gmail.com',
        'subject' => 'Testing Resend with Laravel 3',
        'html' => (new Welcome('kevin'))->render(),
    ]);
    
});