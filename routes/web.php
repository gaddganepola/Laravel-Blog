<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//user related routes

Route::get('/', [UserController::class, 'showCorrectHomepage'])->name('login');

Route::post('/register', [UserController::class, 'register'])->middleware('guest');

Route::post('/login', [UserController::class, 'login'])->middleware('guest');

Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');

//blog post related routes

Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware('mustBeLoggedIn');

Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('auth');

Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);

//profile related routes

Route::get('/profile/{user:username}', [UserController::class, 'profile']);
