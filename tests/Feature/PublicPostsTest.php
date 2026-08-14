<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\get;

test('página inicial lista apenas posts publicados', function () {
    Post::factory()->published()->create(['title' => 'Post publicado']);
    Post::factory()->create(['title' => 'Post rascunho']);

    get('/')
        ->assertOk()
        ->assertSee('Post publicado')
        ->assertDontSee('Post rascunho');
});

test('página inicial ordena os posts do mais recente pro mais antigo', function () {
    Post::factory()->published()->create(['title' => 'Post antigo', 'published_at' => now()->subDays(2)]);
    Post::factory()->published()->create(['title' => 'Post novo', 'published_at' => now()->subDay()]);

    get('/')->assertSeeInOrder(['Post novo', 'Post antigo']);
});

test('o post mais recente aparece em destaque e o restante é paginado no grid, 9 por página', function () {
    Post::factory()->create([
        'title' => 'Post mais recente',
        'status' => 'published',
        'published_at' => now(),
    ]);

    Post::factory()->count(10)->sequence(fn ($sequence) => [
        'status' => 'published',
        'published_at' => now()->subDays($sequence->index + 1),
    ])->create();

    $component = Livewire::test('post-explorer')->assertSee('Post mais recente');

    expect($component->instance()->featured->title)->toBe('Post mais recente')
        ->and($component->instance()->posts->total())->toBe(10)
        ->and($component->instance()->posts->count())->toBe(9)
        ->and($component->instance()->posts->hasMorePages())->toBeTrue();
});

test('listagem usa eager loading para evitar consultas N+1 ao carregar autor e categorias', function () {
    Post::factory()->published()->count(5)->create();

    DB::enableQueryLog();
    Livewire::test('post-explorer');
    $queriesForFewPosts = collect(DB::getQueryLog())
        ->filter(fn ($entry) => str_contains($entry['query'], 'from "users"'))
        ->count();
    DB::flushQueryLog();

    Post::factory()->published()->count(20)->create();

    DB::enableQueryLog();
    Livewire::test('post-explorer');
    $queriesForManyPosts = collect(DB::getQueryLog())
        ->filter(fn ($entry) => str_contains($entry['query'], 'from "users"'))
        ->count();

    expect($queriesForManyPosts)->toBe($queriesForFewPosts);
});

test('página do post publicado retorna 200 com o conteúdo', function () {
    $post = Post::factory()->published()->create([
        'title' => 'Meu post',
        'content' => 'Conteúdo completo do post.',
    ]);

    get(route('posts.show', $post->slug))
        ->assertOk()
        ->assertSee('Meu post')
        ->assertSee('Conteúdo completo do post.');
});

test('post em rascunho retorna 404 na página pública', function () {
    $post = Post::factory()->create();

    get(route('posts.show', $post->slug))->assertNotFound();
});

test('slug inexistente retorna 404', function () {
    get('/posts/slug-que-nao-existe')->assertNotFound();
});

test('posts de um colaborador com acesso removido continuam visíveis com o nome dele', function () {
    $removed = User::factory()->removed()->create(['name' => 'Ex-Colaborador']);
    $post = Post::factory()->for($removed)->published()->create(['title' => 'Post antigo do ex-colaborador']);

    get('/')
        ->assertOk()
        ->assertSee('Post antigo do ex-colaborador')
        ->assertSee('Ex-Colaborador');

    get(route('posts.show', $post->slug))
        ->assertOk()
        ->assertSee('Ex-Colaborador');
});
