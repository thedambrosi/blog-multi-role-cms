<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Storage::fake('public');
});

test('categorias selecionadas são associadas ao criar um post', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Tutoriais']);
    actingAs($user);

    Livewire::test('post-form', ['scope' => 'own'])
        ->set('title', 'Post com categoria')
        ->set('content', 'Conteúdo.')
        ->set('categories', [$category->id])
        ->call('save');

    $post = Post::first();

    expect($post->categories->pluck('id')->all())->toBe([$category->id]);
});

test('editar um post atualiza as categorias associadas', function () {
    $user = User::factory()->create();
    $old = Category::factory()->create(['name' => 'Antiga']);
    $new = Category::factory()->create(['name' => 'Nova']);

    $post = Post::factory()->for($user)->create();
    $post->categories()->attach($old);

    actingAs($user);

    Livewire::test('post-form', ['post' => $post, 'scope' => 'own'])
        ->set('title', $post->title)
        ->set('content', $post->content)
        ->set('categories', [$new->id])
        ->call('save');

    expect($post->fresh()->categories->pluck('id')->all())->toBe([$new->id]);
});

test('colaborador cria um post e o slug é gerado a partir do título', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test('post-form', ['scope' => 'own'])
        ->set('title', 'Meu Primeiro Post')
        ->set('content', 'Conteúdo do post.')
        ->call('save');

    $post = Post::first();

    expect($post)->not->toBeNull()
        ->and($post->title)->toBe('Meu Primeiro Post')
        ->and($post->slug)->toBe('meu-primeiro-post')
        ->and($post->user_id)->toBe($user->id)
        ->and($post->status)->toBe('draft');
});

test('slugs duplicados recebem um sufixo incremental', function () {
    $user = User::factory()->create();
    Post::factory()->for($user)->create(['title' => 'Título Repetido', 'slug' => 'titulo-repetido']);

    actingAs($user);

    Livewire::test('post-form', ['scope' => 'own'])
        ->set('title', 'Título Repetido')
        ->set('content', 'Outro conteúdo.')
        ->call('save');

    expect(Post::where('slug', 'titulo-repetido-1')->exists())->toBeTrue();
});

test('colaborador edita o próprio post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create(['title' => 'Título antigo']);
    actingAs($user);

    Livewire::test('post-form', ['post' => $post, 'scope' => 'own'])
        ->set('title', 'Título novo')
        ->set('content', $post->content)
        ->call('save');

    expect($post->fresh()->title)->toBe('Título novo');
});

test('colaborador não consegue editar post de outro colaborador via o componente', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    actingAs($other);

    Livewire::test('post-form', ['post' => $post, 'scope' => 'own'])
        ->assertStatus(403);
});

test('upload de imagem válida é aceito', function () {
    $user = User::factory()->create();
    actingAs($user);

    $file = UploadedFile::fake()->image('foto.jpg');

    Livewire::test('post-form', ['scope' => 'own'])
        ->set('title', 'Post com imagem')
        ->set('content', 'Conteúdo.')
        ->set('image', $file)
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::first();

    expect($post->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($post->image_path);
});

test('upload de arquivo que não é imagem é rejeitado', function () {
    $user = User::factory()->create();
    actingAs($user);

    $file = UploadedFile::fake()->create('documento.pdf', 100);

    Livewire::test('post-form', ['scope' => 'own'])
        ->set('title', 'Post inválido')
        ->set('content', 'Conteúdo.')
        ->set('image', $file)
        ->call('save')
        ->assertHasErrors(['image']);

    expect(Post::count())->toBe(0);
});

test('upload de imagem maior que 2MB é rejeitado', function () {
    $user = User::factory()->create();
    actingAs($user);

    $file = UploadedFile::fake()->image('grande.jpg')->size(3000);

    Livewire::test('post-form', ['scope' => 'own'])
        ->set('title', 'Post grande')
        ->set('content', 'Conteúdo.')
        ->set('image', $file)
        ->call('save')
        ->assertHasErrors(['image']);

    expect(Post::count())->toBe(0);
});
