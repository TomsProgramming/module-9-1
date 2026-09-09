<?php

use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/post/{post:slug}', [PostController::class, 'show'])->name('post.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::post('/posts', [PostController::class, 'store'])
    ->middleware('auth')
    ->name('posts.store');

Route::get('/posts/{post}/edit', [PostController::class, 'edit'])
    ->middleware('auth')
    ->name('posts.edit');

Route::put('/posts/{post}', [PostController::class, 'update'])
    ->middleware('auth')
    ->name('posts.update');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::post('/posts/{post}/approve', [AdminPostController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/reject', [AdminPostController::class, 'reject'])->name('posts.reject');
    Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');
});

Route::post('/post/{post:slug}/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.store');
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:6,1')
        ->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
