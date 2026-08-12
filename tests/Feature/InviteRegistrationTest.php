<?php

use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

test('convite válido cria usuário colaborador, marca o convite como usado e loga automaticamente', function () {
    $invite = Invite::factory()->create(['email' => 'novo@exemplo.com']);

    Livewire::test('invite-registration-form', ['token' => $invite->token])
        ->set('name', 'Nova Colaboradora')
        ->set('password', 'senha1234')
        ->set('password_confirmation', 'senha1234')
        ->call('register')
        ->assertRedirect(route('painel'));

    $user = User::where('email', 'novo@exemplo.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Nova Colaboradora')
        ->and($user->role)->toBe('colaborador');

    expect($invite->fresh()->isUsed())->toBeTrue();
    expect(Auth::id())->toBe($user->id);
});

test('senha com menos de 8 caracteres falha na validação', function () {
    $invite = Invite::factory()->create();

    Livewire::test('invite-registration-form', ['token' => $invite->token])
        ->set('name', 'Fulano')
        ->set('password', '1234567')
        ->set('password_confirmation', '1234567')
        ->call('register')
        ->assertHasErrors(['password']);

    expect(User::where('role', 'colaborador')->count())->toBe(0);
});

test('confirmação de senha diferente falha na validação', function () {
    $invite = Invite::factory()->create();

    Livewire::test('invite-registration-form', ['token' => $invite->token])
        ->set('name', 'Fulano')
        ->set('password', 'senha1234')
        ->set('password_confirmation', 'outrasenha')
        ->call('register')
        ->assertHasErrors(['password']);

    expect(User::where('role', 'colaborador')->count())->toBe(0);
});

test('convite expirado não permite criar usuário mesmo enviando o formulário diretamente', function () {
    $invite = Invite::factory()->expired()->create();

    Livewire::test('invite-registration-form', ['token' => $invite->token])
        ->set('name', 'Fulano')
        ->set('password', 'senha1234')
        ->set('password_confirmation', 'senha1234')
        ->call('register')
        ->assertStatus(404);

    expect(User::where('role', 'colaborador')->count())->toBe(0);
});

test('convite já usado não permite criar um novo usuário', function () {
    $invite = Invite::factory()->used()->create();

    Livewire::test('invite-registration-form', ['token' => $invite->token])
        ->set('name', 'Fulano')
        ->set('password', 'senha1234')
        ->set('password_confirmation', 'senha1234')
        ->call('register')
        ->assertStatus(404);

    expect(User::where('role', 'colaborador')->count())->toBe(0);
});

test('rate limiting bloqueia o cadastro por convite após muitas tentativas', function () {
    $invite = Invite::factory()->create();

    foreach (range(1, 10) as $attempt) {
        Livewire::test('invite-registration-form', ['token' => $invite->token])
            ->set('name', 'Fulano')
            ->set('password', 'senha1234')
            ->set('password_confirmation', 'senha-diferente')
            ->call('register');
    }

    Livewire::test('invite-registration-form', ['token' => $invite->token])
        ->set('name', 'Fulano')
        ->set('password', 'senha1234')
        ->set('password_confirmation', 'senha-diferente')
        ->call('register')
        ->assertHasErrors(['name']);

    expect(User::where('role', 'colaborador')->count())->toBe(0);
});
