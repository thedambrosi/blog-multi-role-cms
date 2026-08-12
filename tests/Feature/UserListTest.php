<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('admin remove o acesso de um colaborador', function () {
    $admin = User::factory()->admin()->create();
    $colaborador = User::factory()->create();

    actingAs($admin);

    Livewire::test('user-list')
        ->call('removeAccess', $colaborador->id);

    expect($colaborador->fresh()->is_active)->toBeFalse();
});

test('admin não consegue remover o próprio acesso', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin);

    Livewire::test('user-list')
        ->call('removeAccess', $admin->id)
        ->assertStatus(403);

    expect($admin->fresh()->is_active)->toBeTrue();
});

test('colaborador não consegue remover o acesso de ninguém', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    actingAs($user);

    Livewire::test('user-list')
        ->call('removeAccess', $other->id)
        ->assertStatus(403);

    expect($other->fresh()->is_active)->toBeTrue();
});

test('listagem mostra usuários com acesso removido de forma diferenciada', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->removed()->create(['name' => 'Beltrano Removido']);

    actingAs($admin);

    Livewire::test('user-list')
        ->assertSee('Beltrano Removido')
        ->assertSee('acesso removido');
});
