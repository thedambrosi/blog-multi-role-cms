<?php

use App\Models\Invite;

use function Pest\Laravel\get;

test('convite inexistente mostra página de convite inválido', function () {
    get('/convite/token-invalido')
        ->assertNotFound()
        ->assertSee('Convite inválido ou expirado');
});

test('convite expirado mostra página de convite inválido', function () {
    $invite = Invite::factory()->expired()->create();

    get("/convite/{$invite->token}")
        ->assertNotFound()
        ->assertSee('Convite inválido ou expirado');
});

test('convite já usado mostra página de convite inválido', function () {
    $invite = Invite::factory()->used()->create();

    get("/convite/{$invite->token}")
        ->assertNotFound()
        ->assertSee('Convite inválido ou expirado');
});

test('convite válido mostra o formulário de cadastro', function () {
    $invite = Invite::factory()->create();

    get("/convite/{$invite->token}")
        ->assertOk()
        ->assertSeeLivewire('invite-registration-form');
});
