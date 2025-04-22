<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage');
});

Route::get('/about', function () {
    return view('single-post');
});

Route::post('/register', [UserController::class, 'register']);