<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InviteController;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;
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

    Route::middleware(['auth', 'role:admin,colaborador'])->group(function () {
        Route::view('/', 'painel')->name('painel');

        Route::get('/posts', fn () => view('posts.index', ['scope' => 'own']))
            ->name('painel.posts.index');

        Route::get('/posts/create', fn () => view('posts.form', ['scope' => 'own', 'post' => null]))
            ->name('painel.posts.create');

        Route::get('/posts/{post}/edit', function (Post $post) {
            Gate::authorize('update', $post);

            return view('posts.form', ['scope' => 'own', 'post' => $post]);
        })->name('painel.posts.edit');
    });
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/', 'admin')->name('admin');

    Route::get('/posts', fn () => view('posts.index', ['scope' => 'all']))
        ->name('admin.posts.index');

    Route::get('/posts/create', fn () => view('posts.form', ['scope' => 'all', 'post' => null]))
        ->name('admin.posts.create');

    Route::get('/posts/{post}/edit', function (Post $post) {
        Gate::authorize('update', $post);

        return view('posts.form', ['scope' => 'all', 'post' => $post]);
    })->name('admin.posts.edit');
});
