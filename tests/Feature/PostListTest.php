<?php

use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('colaborador só vê os próprios posts no painel', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Post::factory()->for($user)->create(['title' => 'Post do usuário']);
    Post::factory()->for($other)->create(['title' => 'Post de outro']);

    actingAs($user);

    Livewire::test('post-list', ['scope' => 'own'])
        ->assertSee('Post do usuário')
        ->assertDontSee('Post de outro');
});

test('admin vê todos os posts no admin', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->create();

    Post::factory()->for($author)->create(['title' => 'Post do colaborador']);

    actingAs($admin);

    Livewire::test('post-list', ['scope' => 'all'])
        ->assertSee('Post do colaborador');
});

test('publicar um post muda o status e define published_at', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create(['status' => 'draft', 'published_at' => null]);

    actingAs($user);

    Livewire::test('post-list', ['scope' => 'own'])
        ->call('togglePublish', $post->id);

    $post->refresh();

    expect($post->status)->toBe('published');
    expect($post->published_at)->not->toBeNull();
});

test('despublicar um post volta o status para rascunho', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->published()->create();

    actingAs($user);

    Livewire::test('post-list', ['scope' => 'own'])
        ->call('togglePublish', $post->id);

    $post->refresh();

    expect($post->status)->toBe('draft');
    expect($post->published_at)->toBeNull();
});

test('colaborador não consegue publicar post de outro colaborador', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    actingAs($other);

    Livewire::test('post-list', ['scope' => 'all'])
        ->call('togglePublish', $post->id)
        ->assertStatus(403);
});

test('colaborador consegue excluir o próprio post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    actingAs($user);

    Livewire::test('post-list', ['scope' => 'own'])
        ->call('delete', $post->id);

    expect(Post::find($post->id))->toBeNull();
});

test('colaborador não consegue excluir post de outro colaborador', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    actingAs($other);

    Livewire::test('post-list', ['scope' => 'all'])
        ->call('delete', $post->id)
        ->assertStatus(403);

    expect(Post::find($post->id))->not->toBeNull();
});

test('admin consegue excluir post de qualquer colaborador', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->create();
    $post = Post::factory()->for($author)->create();

    actingAs($admin);

    Livewire::test('post-list', ['scope' => 'all'])
        ->call('delete', $post->id);

    expect(Post::find($post->id))->toBeNull();
});
