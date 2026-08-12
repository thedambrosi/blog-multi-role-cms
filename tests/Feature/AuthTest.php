<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('formulário de login é exibido', function () {
    get(route('login'))
        ->assertOk()
        ->assertSee('Entrar');
});

test('colaborador loga com credenciais válidas e é redirecionado para o painel', function () {
    $user = User::factory()->create(['password' => 'senha1234']);

    post(route('login.store'), [
        'email' => $user->email,
        'password' => 'senha1234',
    ])->assertRedirect(route('painel'));

    expect(auth()->id())->toBe($user->id);
});

test('admin loga com credenciais válidas e é redirecionado para o admin', function () {
    $admin = User::factory()->admin()->create(['password' => 'senha1234']);

    post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'senha1234',
    ])->assertRedirect(route('admin'));

    expect(auth()->id())->toBe($admin->id);
});

test('login com credenciais inválidas falha e não autentica', function () {
    $user = User::factory()->create(['password' => 'senha1234']);

    post(route('login.store'), [
        'email' => $user->email,
        'password' => 'senha-errada',
    ])->assertSessionHasErrors('email');

    expect(auth()->check())->toBeFalse();
});

test('rate limiting bloqueia o login após 5 tentativas erradas com o mesmo email e IP', function () {
    $user = User::factory()->create(['password' => 'senha1234']);

    foreach (range(1, 5) as $attempt) {
        post(route('login.store'), [
            'email' => $user->email,
            'password' => 'senha-errada',
        ]);
    }

    post(route('login.store'), [
        'email' => $user->email,
        'password' => 'senha-errada',
    ])->assertStatus(429);
});

test('usuário autenticado consegue fazer logout', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    expect(auth()->check())->toBeFalse();
});
