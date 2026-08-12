<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InviteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/convite/{token}', [InviteController::class, 'show'])->name('invite.show');

Route::prefix('painel')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [AuthController::class, 'store'])
        ->middleware(['guest', 'throttle:login'])
        ->name('login.store');

    Route::post('/logout', [AuthController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    Route::view('/', 'painel')
        ->middleware(['auth', 'role:admin,colaborador'])
        ->name('painel');
});

Route::view('/admin', 'admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin');
