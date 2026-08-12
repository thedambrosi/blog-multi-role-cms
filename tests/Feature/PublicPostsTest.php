<?php

use App\Models\Post;
use Illuminate\Support\Facades\DB;

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

test('página inicial pagina os posts, 12 por página', function () {
    Post::factory()->published()->count(13)->create();

    $response = get('/');

    $response->assertOk();
    expect($response->viewData('posts')->count())->toBe(12);
    expect($response->viewData('posts')->hasMorePages())->toBeTrue();
});

test('listagem usa eager loading para evitar consultas N+1 ao carregar o autor', function () {
    Post::factory()->published()->count(5)->create();

    DB::enableQueryLog();

    get('/');

    $userQueries = collect(DB::getQueryLog())
        ->filter(fn ($entry) => str_contains($entry['query'], 'users'));

    expect($userQueries)->toHaveCount(1);
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
