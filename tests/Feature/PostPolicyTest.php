<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('colaborador não consegue acessar a edição de post de outro colaborador', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    actingAs($other)
        ->get(route('painel.posts.edit', $post))
        ->assertForbidden();
});

test('colaborador consegue acessar a edição do próprio post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    actingAs($user)
        ->get(route('painel.posts.edit', $post))
        ->assertOk();
});

test('admin consegue acessar a edição de post de qualquer colaborador', function () {
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    actingAs($admin)
        ->get(route('admin.posts.edit', $post))
        ->assertOk();
});
