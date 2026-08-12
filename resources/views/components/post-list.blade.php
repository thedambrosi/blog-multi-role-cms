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

<div class="mt-8 space-y-3">
    @forelse ($this->posts as $post)
    <div wire:key="post-{{ $post->id }}" class="flex flex-wrap items-center gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
        @if ($post->imageUrl())
        <img src="{{ $post->imageUrl() }}" alt="" class="h-14 w-14 flex-shrink-0 rounded-lg object-cover">
        @else
        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100">
            <x-heroicon-o-photo class="h-6 w-6 text-gray-300" />
        </div>
        @endif

        <div class="min-w-0 flex-1">
            <p class="truncate font-medium text-gray-900">{{ $post->title }}</p>
            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                @if ($post->isPublished())
                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 font-medium text-green-700">
                    <x-heroicon-m-check-circle class="h-3.5 w-3.5" />
                    Publicado
                </span>
                @else
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 font-medium text-amber-700">
                    <x-heroicon-m-pencil class="h-3.5 w-3.5" />
                    Rascunho
                </span>
                @endif

                @if ($scope === 'all')
                <span>&middot;</span>
                <span class="flex items-center gap-1">
                    <x-heroicon-m-user class="h-3.5 w-3.5" />
                    {{ $post->user->name }}
                </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4 text-sm">
            @can('update', $post)
            <a href="{{ route("{$this->routeBase()}.posts.edit", $post) }}"
                class="flex items-center gap-1.5 text-gray-500 hover:text-gray-900">
                <x-heroicon-m-pencil-square class="h-4 w-4" />
                Editar
            </a>
            <button type="button" wire:click="togglePublish({{ $post->id }})"
                wire:loading.attr="disabled" wire:target="togglePublish({{ $post->id }})"
                class="flex items-center gap-1.5 text-gray-500 hover:text-gray-900 disabled:opacity-50">
                <x-heroicon-m-eye class="h-4 w-4" wire:loading.remove wire:target="togglePublish({{ $post->id }})" />
                <x-heroicon-m-arrow-path class="h-4 w-4 animate-spin" wire:loading wire:target="togglePublish({{ $post->id }})" />
                {{ $post->isPublished() ? 'Despublicar' : 'Publicar' }}
            </button>
            @endcan
            @can('delete', $post)
            <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Excluir este post? Essa ação não pode ser desfeita."
                wire:loading.attr="disabled" wire:target="delete({{ $post->id }})"
                class="flex items-center gap-1.5 text-red-500 hover:text-red-700 disabled:opacity-50">
                <x-heroicon-m-trash class="h-4 w-4" />
                Excluir
            </button>
            @endcan
        </div>
    </div>
    @empty
    <div class="rounded-xl border border-dashed border-gray-300 px-6 py-16 text-center">
        <x-heroicon-o-document-text class="mx-auto h-10 w-10 text-gray-300" />
        <p class="mt-3 text-sm text-gray-500">Nenhum post por aqui ainda.</p>
    </div>
    @endforelse
</div>
