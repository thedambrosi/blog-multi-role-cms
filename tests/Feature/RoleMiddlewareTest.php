<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('visitante não autenticado é redirecionado para o login ao acessar o painel', function () {
    get('/painel')->assertRedirect(route('login'));
});

test('visitante não autenticado é redirecionado para o login ao acessar o admin', function () {
    get('/admin')->assertRedirect(route('login'));
});

test('colaborador acessa o painel', function () {
    $user = User::factory()->create();

    actingAs($user)->get('/painel')->assertOk();
});

test('colaborador não consegue acessar o admin', function () {
    $user = User::factory()->create();

    actingAs($user)->get('/admin')->assertForbidden();
});

test('admin acessa tanto o painel quanto o admin', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin)->get('/painel')->assertOk();
    actingAs($admin)->get('/admin')->assertOk();
});
