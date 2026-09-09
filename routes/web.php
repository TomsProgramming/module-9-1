<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/post/{post:slug}', [PostController::class, 'show'])->name('post.show');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
