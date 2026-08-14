<?php

use App\Models\Category;
use App\Models\Post;
use Livewire\Livewire;

test('busca filtra posts pelo título', function () {
    Post::factory()->published()->create(['title' => 'Como cuidar de um gato']);
    Post::factory()->published()->create(['title' => 'Receita de bolo de cenoura']);

    Livewire::test('post-explorer')
        ->set('search', 'gato')
        ->assertSee('Como cuidar de um gato')
        ->assertDontSee('Receita de bolo de cenoura');
});

test('busca filtra posts pelo conteúdo', function () {
    Post::factory()->published()->create(['title' => 'Título genérico A', 'content' => 'Tudo sobre adestramento canino.']);
    Post::factory()->published()->create(['title' => 'Título genérico B', 'content' => 'Tudo sobre jardinagem.']);

    Livewire::test('post-explorer')
        ->set('search', 'adestramento')
        ->assertSee('Título genérico A')
        ->assertDontSee('Título genérico B');
});

test('busca sem resultados mostra estado vazio', function () {
    Post::factory()->published()->create(['title' => 'Único post']);

    Livewire::test('post-explorer')
        ->set('search', 'termo que não existe')
        ->assertSee('Nenhum post encontrado com esses filtros.');
});

test('filtro por categoria mostra apenas posts daquela categoria', function () {
    $caes = Category::factory()->create(['name' => 'Cães']);
    $gatos = Category::factory()->create(['name' => 'Gatos']);

    $postCaes = Post::factory()->published()->create(['title' => 'Post sobre cães']);
    $postCaes->categories()->attach($caes);

    $postGatos = Post::factory()->published()->create(['title' => 'Post sobre gatos']);
    $postGatos->categories()->attach($gatos);

    Livewire::test('post-explorer')
        ->call('toggleCategory', $caes->slug)
        ->assertSee('Post sobre cães')
        ->assertDontSee('Post sobre gatos');
});

test('clicar de novo na mesma categoria limpa o filtro', function () {
    $caes = Category::factory()->create(['name' => 'Cães']);
    $post = Post::factory()->published()->create(['title' => 'Post sobre cães']);
    $post->categories()->attach($caes);

    $otherPost = Post::factory()->published()->create(['title' => 'Outro post']);

    Livewire::test('post-explorer')
        ->call('toggleCategory', $caes->slug)
        ->assertSet('category', $caes->slug)
        ->call('toggleCategory', $caes->slug)
        ->assertSet('category', null)
        ->assertSee('Outro post');
});

test('apenas categorias com posts publicados aparecem como filtro', function () {
    $comPosts = Category::factory()->create(['name' => 'Com Posts']);
    $semPosts = Category::factory()->create(['name' => 'Sem Posts']);

    $post = Post::factory()->published()->create();
    $post->categories()->attach($comPosts);

    Livewire::test('post-explorer')
        ->assertSee('Com Posts')
        ->assertDontSee('Sem Posts');
});

test('post em destaque não aparece duplicado no grid', function () {
    $latest = Post::factory()->published()->create(['title' => 'Post mais novo', 'published_at' => now()]);
    Post::factory()->published()->create(['title' => 'Post antigo', 'published_at' => now()->subDay()]);

    $component = Livewire::test('post-explorer');

    expect($component->instance()->featured->id)->toBe($latest->id)
        ->and($component->instance()->posts->pluck('id'))->not->toContain($latest->id);
});

test('destaque some quando há busca ou filtro ativo', function () {
    Post::factory()->published()->create(['title' => 'Post mais novo']);

    $component = Livewire::test('post-explorer')
        ->set('search', 'novo')
        ->assertDontSee('Destaque');

    expect($component->instance()->featured)->toBeNull();
});
