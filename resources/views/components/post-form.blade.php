<?php

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function mount(?Post $post = null, string $scope = 'own'): void
    {
        $this->scope = $scope;

        if ($post) {
            $this->authorize('update', $post);

            $this->post = $post;
            $this->title = $post->title;
            $this->content = $post->content;
        } else {
            $this->authorize('create', Post::class);
        }
    }

    public function save(): void
    {
        $this->authorize($this->post ? 'update' : 'create', $this->post ?? Post::class);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = [
            'title' => $this->title,
            'content' => $this->content,
        ];

        if ($this->image) {
            $data['image_path'] = $this->image->store('posts', 'public');
        }

        if ($this->post) {
            $oldImagePath = $this->post->image_path;

            $this->post->update($data);

            if ($this->image && $oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            $post = new Post($data);
            $post->user_id = Auth::id();
            $post->save();
        }

        $this->redirect(route("{$this->indexRouteBase()}.posts.index"));
    }

    protected function indexRouteBase(): string
    {
        return $this->scope === 'all' ? 'admin' : 'painel';
    }
}; ?>

<div>
    <h1 class="text-xl font-semibold text-gray-900">{{ $post ? 'Editar post' : 'Novo post' }}</h1>

    <form wire:submit="save" class="mt-6 space-y-4">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
            <input id="title" type="text" wire:model="title"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-500 focus:outline-none">
            @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Conteúdo</label>
            <textarea id="content" wire:model="content" rows="10"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-500 focus:outline-none"></textarea>
            @error('content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Imagem</label>

            @if ($post?->image_path && ! $image)
            <img src="{{ Storage::disk('public')->url($post->image_path) }}" alt="" class="mt-2 h-32 w-auto rounded-md object-cover">
            @endif

            @if ($image && $image->isPreviewable())
            <img src="{{ $image->temporaryUrl() }}" alt="" class="mt-2 h-32 w-auto rounded-md object-cover">
            @endif

            <input id="image" type="file" wire:model="image"
                class="mt-1 block w-full text-sm text-gray-700">
            <p class="mt-1 text-xs text-gray-500">JPG, PNG ou WEBP, até 2MB.</p>
            @error('image')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" wire:loading.attr="disabled"
                class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 disabled:opacity-50">
                Salvar
            </button>
            <a href="{{ route("{$this->indexRouteBase()}.posts.index") }}" class="text-sm text-gray-500 hover:text-gray-700">
                Cancelar
            </a>
        </div>
    </form>
</div>
