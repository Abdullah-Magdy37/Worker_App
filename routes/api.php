<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\WorkerAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('DbBackup')->prefix('auth')->group(function () {

    Route::controller(AdminController::class)->prefix('admin')->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refresh');
        Route::get('/user-profile', 'userProfile');
    });

    Route::controller(WorkerAuthController::class)->prefix('worker')->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refresh');
        Route::get('/user-profile', 'workerProfile');
        Route::get('/verify/{token}', 'verify');
    });

    Route::controller(PostsController::class)->prefix('worker')->group(function () {
        Route::post('addPost', 'addPost');
    });

    Route::controller(ClientAuthController::class)->prefix('client')->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refresh');
        Route::get('/user-profile', 'workerProfile');
    });
});

    Route::get('Unauthorized', function () {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    })->name('login');
