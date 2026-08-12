<?php

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public string $scope = 'own';

    #[Computed]
    public function posts()
    {
        return Post::query()
            ->with('user')
            ->when($this->scope !== 'all', fn ($query) => $query->where('user_id', Auth::id()))
            ->latest()
            ->get();
    }

    public function togglePublish(Post $post): void
    {
        $this->authorize('update', $post);

        $post->forceFill(
            $post->isPublished()
                ? ['status' => 'draft', 'published_at' => null]
                : ['status' => 'published', 'published_at' => now()]
        )->save();

        unset($this->posts);
    }

    public function delete(Post $post): void
    {
        $this->authorize('delete', $post);

        $post->delete();

        unset($this->posts);
    }

    public function routeBase(): string
    {
        return $this->scope === 'all' ? 'admin' : 'painel';
    }
}; ?>

<div class="mt-8 space-y-4">
    @forelse ($this->posts as $post)
    <div class="flex items-center justify-between rounded-md border border-gray-200 px-4 py-3">
        <div>
            <p class="text-sm font-medium text-gray-900">{{ $post->title }}</p>
            <p class="mt-1 text-xs text-gray-500">
                @if ($scope === 'all')
                {{ $post->user->name }} ·
                @endif
                <span class="{{ $post->isPublished() ? 'text-green-600' : 'text-gray-500' }}">
                    {{ $post->isPublished() ? 'Publicado' : 'Rascunho' }}
                </span>
            </p>
        </div>

        <div class="flex items-center gap-3 text-sm">
            @can('update', $post)
            <a href="{{ route("{$this->routeBase()}.posts.edit", $post) }}" class="text-gray-600 hover:text-gray-900">
                Editar
            </a>
            <button type="button" wire:click="togglePublish({{ $post->id }})" class="text-gray-600 hover:text-gray-900">
                {{ $post->isPublished() ? 'Despublicar' : 'Publicar' }}
            </button>
            @endcan
            @can('delete', $post)
            <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Excluir este post?" class="text-red-600 hover:text-red-800">
                Excluir
            </button>
            @endcan
        </div>
    </div>
    @empty
    <p class="text-sm text-gray-500">Nenhum post por aqui ainda.</p>
    @endforelse
</div>
