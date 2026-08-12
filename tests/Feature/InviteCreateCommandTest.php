<?php

use App\Models\Invite;
use App\Models\User;

test('comando invite:create gera um convite válido para o email informado', function () {
    User::factory()->admin()->create();

    $this->artisan('invite:create', ['email' => 'convidado@exemplo.com'])
        ->assertSuccessful();

    $invite = Invite::first();

    expect($invite)->not->toBeNull()
        ->and($invite->email)->toBe('convidado@exemplo.com')
        ->and(strlen($invite->token))->toBe(40)
        ->and($invite->isExpired())->toBeFalse()
        ->and($invite->isUsed())->toBeFalse();
});

test('comando invite:create falha se não existir nenhum admin', function () {
    $this->artisan('invite:create', ['email' => 'convidado@exemplo.com'])
        ->assertFailed();

    expect(Invite::count())->toBe(0);
});
