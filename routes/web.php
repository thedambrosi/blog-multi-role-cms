<?php

use App\Http\Controllers\InviteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/convite/{token}', [InviteController::class, 'show'])->name('invite.show');

Route::view('/painel', 'painel')->middleware('auth')->name('painel');
