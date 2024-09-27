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
    $mail = new Welcome('Kevin');

    Mail::to('kevinguidou@gmail.com')->send($mail);

    // Resend::emails()->send([
    //     'from' => 'Acme <onboarding@resend.dev>',
    //     'to' => 'kevinguidou@gmail.com',
    //     'subject' => 'hello world',
    //     'html' => (new Welcome('kevin'))->render(),
    // ]);
    
});