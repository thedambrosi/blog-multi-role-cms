<?php

use App\Models\Invite;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('admin gera um convite pela interface e vê o link completo', function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin);

    Livewire::test('invite-create-form')
        ->set('email', 'convidado@exemplo.com')
        ->call('create')
        ->assertSet('inviteUrl', fn ($url) => str_ends_with($url, '/convite/'.Invite::first()->token));

    $invite = Invite::first();

    expect($invite)->not->toBeNull()
        ->and($invite->email)->toBe('convidado@exemplo.com')
        ->and($invite->created_by)->toBe($admin->id)
        ->and(strlen($invite->token))->toBe(40)
        ->and($invite->isExpired())->toBeFalse()
        ->and($invite->expires_at->diffInHours(now()))->toBeLessThanOrEqual(48);
});

test('email inválido falha na validação e não cria convite', function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin);

    Livewire::test('invite-create-form')
        ->set('email', 'não é um email')
        ->call('create')
        ->assertHasErrors(['email']);

    expect(Invite::count())->toBe(0);
});

test('colaborador não consegue gerar convite pelo componente', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test('invite-create-form')->assertStatus(403);
});

test('rate limiting bloqueia a geração de convites após muitas tentativas', function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin);

    foreach (range(1, 20) as $attempt) {
        Livewire::test('invite-create-form')
            ->set('email', "convidado{$attempt}@exemplo.com")
            ->call('create');
    }

    Livewire::test('invite-create-form')
        ->set('email', 'mais-um@exemplo.com')
        ->call('create')
        ->assertHasErrors(['email']);

    expect(Invite::count())->toBe(20);
});
