<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('visitante não autenticado é redirecionado para o login ao acessar /admin/users', function () {
    get('/admin/users')->assertRedirect(route('login'));
});

test('colaborador não consegue acessar /admin/users', function () {
    $user = User::factory()->create();

    actingAs($user)->get('/admin/users')->assertForbidden();
});

test('colaborador não consegue acessar /admin/invites/create', function () {
    $user = User::factory()->create();

    actingAs($user)->get('/admin/invites/create')->assertForbidden();
});

test('admin acessa a listagem de usuários', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'Fulano Colaborador']);

    actingAs($admin)
        ->get('/admin/users')
        ->assertOk()
        ->assertSee('Fulano Colaborador')
        ->assertSee('Convidar colaborador');
});

test('admin acessa a página de gerar convite', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin)->get('/admin/invites/create')->assertOk();
});
