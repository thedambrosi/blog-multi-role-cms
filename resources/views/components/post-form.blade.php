<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    #[Locked]
    public ?Post $post = null;

    #[Locked]
    public string $scope = 'own';

    public string $title = '';

    public string $content = '';

    public $image = null;

    public array $categories = [];

    public function mount(?Post $post = null, string $scope = 'own'): void
    {
        $this->scope = $scope;

        if ($post) {
            $this->authorize('update', $post);

            $this->post = $post;
            $this->title = $post->title;
            $this->content = $post->content;
            $this->categories = $post->categories->pluck('id')->map(fn ($id) => (string) $id)->all();
        } else {
            $this->authorize('create', Post::class);
        }
    }

    #[Computed]
    public function availableCategories()
    {
        return Category::query()->orderBy('name')->get();
    }

    public function save(): void
    {
        $this->authorize($this->post ? 'update' : 'create', $this->post ?? Post::class);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'categories' => ['array'],
            'categories.*' => ['exists:categories,id'],
        ]);

        $data = [
            'title' => $this->title,
            'content' => $this->content,
        ];

        if ($this->image) {
            $encoded = Image::decode($this->image)
                ->scaleDown(width: 1200)
                ->encode(new WebpEncoder(quality: 80));

            $path = 'posts/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($path, (string) $encoded);

            $data['image_path'] = $path;
        }

        if ($this->post) {
            $oldImagePath = $this->post->image_path;

            $this->post->update($data);
            $this->post->categories()->sync($this->categories);

            if ($this->image && $oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            $post = new Post($data);
            $post->user_id = Auth::id();
            $post->save();
            $post->categories()->sync($this->categories);
        }

        session()->flash('success', $this->post ? 'Post atualizado com sucesso.' : 'Post criado com sucesso.');

        $this->redirect(route("{$this->indexRouteBase()}.posts.index"));
    }

    protected function indexRouteBase(): string
    {
        return $this->scope === 'all' ? 'admin' : 'painel';
    }
}; ?>

<div class="rounded-xl border border-gray-200 bg-white p-6 sm:p-8">
    <h1 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
        <x-heroicon-o-pencil-square class="h-5 w-5 text-indigo-600" />
        {{ $post ? 'Editar post' : 'Novo post' }}
    </h1>

    <form wire:submit="save" class="mt-6 space-y-5">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
            <input id="title" type="text" wire:model="title"
                class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('title')
            <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                {{ $message }}
            </p>
            @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Conteúdo</label>
            <textarea id="content" wire:model="content" rows="12"
                class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm leading-relaxed shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
            @error('content')
            <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                {{ $message }}
            </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Categorias</label>
            <div class="mt-2 flex flex-wrap gap-2">
                @forelse ($this->availableCategories as $cat)
                <label class="flex cursor-pointer items-center gap-1.5 rounded-full border border-gray-200 px-3 py-1.5 text-sm text-gray-600 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:text-indigo-700">
                    <input type="checkbox" wire:model="categories" value="{{ $cat->id }}"
                        class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    {{ $cat->name }}
                </label>
                @empty
                <p class="text-sm text-gray-400">Nenhuma categoria cadastrada ainda.</p>
                @endforelse
            </div>
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Imagem</label>

            @if ($post?->image_path && ! $image)
            <img src="{{ Storage::disk('public')->url($post->image_path) }}" alt="" class="mt-2 h-32 w-auto rounded-lg object-cover">
            @endif

            @if ($image && $image->isPreviewable())
            <img src="{{ $image->temporaryUrl() }}" alt="" class="mt-2 h-32 w-auto rounded-lg object-cover">
            @endif

            <input id="image" type="file" wire:model="image"
                class="mt-1.5 block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-100 file:px-3.5 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200">
            <p wire:loading wire:target="image" class="mt-1.5 flex items-center gap-1 text-xs text-gray-500">
                <x-heroicon-m-arrow-path class="h-3.5 w-3.5 animate-spin" />
                Enviando imagem...
            </p>
            <p class="mt-1.5 text-xs text-gray-500">JPG, PNG ou WEBP, até 2MB.</p>
            @error('image')
            <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="flex items-center gap-4 border-t border-gray-100 pt-5">
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                class="flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Salvar</span>
                <span wire:loading wire:target="save">Salvando...</span>
            </button>
            <a href="{{ route("{$this->indexRouteBase()}.posts.index") }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                Cancelar
            </a>
        </div>
    </form>
</div>
