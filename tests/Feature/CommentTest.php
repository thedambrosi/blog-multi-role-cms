<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('comentário criado fica com status pending por padrão', function () {
    $comment = Comment::factory()->create();

    expect($comment->status)->toBe('pending');
});

test('apenas comentários aprovados aparecem na página pública do post', function () {
    $post = Post::factory()->published()->create();

    Comment::factory()->for($post)->approved()->create(['name' => 'Fulano Aprovado', 'body' => 'Comentário aprovado']);
    Comment::factory()->for($post)->pending()->create(['name' => 'Ciclano Pendente', 'body' => 'Comentário pendente']);
    Comment::factory()->for($post)->create(['name' => 'Beltrano Rejeitado', 'body' => 'Comentário rejeitado', 'status' => 'rejected']);

    get(route('posts.show', $post->slug))
        ->assertSee('Fulano Aprovado')
        ->assertSee('Comentário aprovado')
        ->assertDontSee('Ciclano Pendente')
        ->assertDontSee('Comentário pendente')
        ->assertDontSee('Beltrano Rejeitado')
        ->assertDontSee('Comentário rejeitado');
});

test('envio de comentário válido cria registro pendente e mostra mensagem de sucesso', function () {
    $post = Post::factory()->published()->create();

    Livewire::test('comment-form', ['post' => $post])
        ->set('name', 'Visitante')
        ->set('email', 'visitante@exemplo.com')
        ->set('body', 'Muito bom esse post, parabéns!')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSee('Comentário enviado! Ele vai aparecer assim que for aprovado.');

    $comment = Comment::first();

    expect($comment)->not->toBeNull()
        ->and($comment->status)->toBe('pending')
        ->and($comment->post_id)->toBe($post->id)
        ->and($comment->name)->toBe('Visitante')
        ->and($comment->email)->toBe('visitante@exemplo.com');
});

test('comentário com linguagem imprópria é rejeitado na validação', function () {
    $post = Post::factory()->published()->create();

    Livewire::test('comment-form', ['post' => $post])
        ->set('name', 'Visitante')
        ->set('email', 'visitante@exemplo.com')
        ->set('body', 'Esse post é uma bosta')
        ->call('submit')
        ->assertHasErrors(['body']);

    expect(Comment::count())->toBe(0);
});

test('rate limiting bloqueia envio de comentários após muitas tentativas', function () {
    $post = Post::factory()->published()->create();

    foreach (range(1, 5) as $attempt) {
        Livewire::test('comment-form', ['post' => $post])
            ->set('name', 'Visitante')
            ->set('email', 'visitante@exemplo.com')
            ->set('body', "Comentário de teste número {$attempt}")
            ->call('submit');
    }

    Livewire::test('comment-form', ['post' => $post])
        ->set('name', 'Visitante')
        ->set('email', 'visitante@exemplo.com')
        ->set('body', 'Mais um comentário')
        ->call('submit')
        ->assertHasErrors(['body']);

    expect(Comment::count())->toBe(5);
});

test('colaborador consegue aprovar comentário do próprio post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->create();

    actingAs($user);

    Livewire::test('comment-list', ['scope' => 'own'])
        ->call('approve', $comment->id);

    expect($comment->fresh()->status)->toBe('approved');
});

test('colaborador consegue rejeitar comentário do próprio post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->approved()->create();

    actingAs($user);

    Livewire::test('comment-list', ['scope' => 'own'])
        ->call('reject', $comment->id);

    expect($comment->fresh()->status)->toBe('rejected');
});

test('colaborador consegue excluir comentário do próprio post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->create();

    actingAs($user);

    Livewire::test('comment-list', ['scope' => 'own'])
        ->call('delete', $comment->id);

    expect(Comment::find($comment->id))->toBeNull();
});

test('colaborador não consegue aprovar comentário de post de outro colaborador', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $comment = Comment::factory()->for($post)->create();

    actingAs($other);

    Livewire::test('comment-list', ['scope' => 'all'])
        ->call('approve', $comment->id)
        ->assertStatus(403);

    expect($comment->fresh()->status)->toBe('pending');
});

test('colaborador não consegue excluir comentário de post de outro colaborador', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $comment = Comment::factory()->for($post)->create();

    actingAs($other);

    Livewire::test('comment-list', ['scope' => 'all'])
        ->call('delete', $comment->id)
        ->assertStatus(403);

    expect(Comment::find($comment->id))->not->toBeNull();
});

test('admin consegue aprovar comentário de qualquer colaborador', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->create();
    $post = Post::factory()->for($author)->create();
    $comment = Comment::factory()->for($post)->create();

    actingAs($admin);

    Livewire::test('comment-list', ['scope' => 'all'])
        ->call('approve', $comment->id);

    expect($comment->fresh()->status)->toBe('approved');
});

test('admin consegue excluir comentário de qualquer colaborador', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->create();
    $post = Post::factory()->for($author)->create();
    $comment = Comment::factory()->for($post)->create();

    actingAs($admin);

    Livewire::test('comment-list', ['scope' => 'all'])
        ->call('delete', $comment->id);

    expect(Comment::find($comment->id))->toBeNull();
});
