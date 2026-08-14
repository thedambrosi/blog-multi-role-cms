<?php

use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    const FEATURED_COUNT = 2;

    #[Url(as: 'busca', history: true)]
    public string $search = '';

    #[Url(as: 'categoria', history: true)]
    public ?string $category = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleCategory(?string $slug): void
    {
        $this->category = $this->category === $slug ? null : $slug;
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->whereHas('posts', fn ($query) => $query->published())
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function featured(): \Illuminate\Support\Collection
    {
        if ($this->search !== '' || $this->category) {
            return collect();
        }

        return Post::query()
            ->published()
            ->with(['user', 'categories'])
            ->latest('published_at')
            ->limit(self::FEATURED_COUNT)
            ->get();
    }

    #[Computed]
    public function posts()
    {
        return Post::query()
            ->published()
            ->with(['user', 'categories'])
            ->when($this->featured->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $this->featured->pluck('id')))
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('title', 'like', "%{$this->search}%")
                        ->orWhere('content', 'like', "%{$this->search}%");
                });
            })
            ->when($this->category, fn ($query) => $query->inCategory($this->category))
            ->latest('published_at')
            ->paginate(9);
    }
}; ?>

<div class="space-y-10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 sm:max-w-xs">
            <x-heroicon-m-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input type="search" wire:model.live.debounce.400ms="search" placeholder="Buscar posts..."
                class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-3.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        @if ($this->categories->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="toggleCategory(null)"
                class="rounded-full px-3 py-1.5 text-xs font-medium transition {{ is_null($category) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Todas
            </button>
            @foreach ($this->categories as $cat)
            <button type="button" wire:click="toggleCategory('{{ $cat->slug }}')"
                class="rounded-full px-3 py-1.5 text-xs font-medium transition {{ $category === $cat->slug ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>
        @endif
    </div>

    @if ($this->featured->isNotEmpty())
    <div class="grid gap-6 sm:grid-cols-2">
        @foreach ($this->featured as $post)
        <a href="{{ route('posts.show', $post->slug) }}" wire:key="featured-{{ $post->id }}"
            class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 transition hover:border-gray-300 hover:shadow-md">
            @if ($post->imageUrl())
            <img src="{{ $post->imageUrl() }}" alt="" class="h-48 w-full object-cover">
            @else
            <div class="flex h-48 items-center justify-center bg-gray-50">
                <x-heroicon-o-photo class="h-10 w-10 text-gray-300" />
            </div>
            @endif

            <div class="flex flex-1 flex-col p-6">
                <span class="inline-flex w-fit items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                    <x-heroicon-m-star class="h-3.5 w-3.5" />
                    Destaque
                </span>

                @if ($post->categories->isNotEmpty())
                <p class="mt-3 text-xs font-medium uppercase tracking-wide text-gray-400">
                    {{ $post->categories->pluck('name')->join(' · ') }}
                </p>
                @endif

                <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900 group-hover:text-indigo-600">
                    {{ $post->title }}
                </h2>
                <p class="mt-3 flex-1 text-sm leading-relaxed text-gray-600">{{ $post->excerpt(140) }}</p>
                <p class="mt-4 flex items-center gap-1.5 text-xs text-gray-500">
                    <x-heroicon-m-calendar class="h-3.5 w-3.5" />
                    {{ $post->published_at->format('d/m/Y') }}
                    <span>&middot;</span>
                    {{ $post->user->name }}
                </p>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    <div>
        @if ($this->posts->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 px-6 py-16 text-center">
            <x-heroicon-o-newspaper class="mx-auto h-10 w-10 text-gray-300" />
            <p class="mt-3 text-sm text-gray-500">
                {{ $search !== '' || $category ? 'Nenhum post encontrado com esses filtros.' : 'Nenhum post publicado ainda.' }}
            </p>
        </div>
        @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->posts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" wire:key="post-{{ $post->id }}"
                class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 transition hover:border-gray-300 hover:shadow-sm">
                @if ($post->imageUrl())
                <img src="{{ $post->imageUrl() }}" alt="" class="h-40 w-full object-cover">
                @else
                <div class="flex h-40 items-center justify-center bg-gray-50">
                    <x-heroicon-o-photo class="h-8 w-8 text-gray-300" />
                </div>
                @endif

                <div class="flex flex-1 flex-col p-5">
                    @if ($post->categories->isNotEmpty())
                    <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">
                        {{ $post->categories->first()->name }}
                    </p>
                    @endif
                    <h3 class="mt-1.5 font-semibold text-gray-900 group-hover:text-indigo-600">{{ $post->title }}</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-gray-600">{{ $post->excerpt(110) }}</p>
                    <p class="mt-4 flex items-center gap-1.5 text-xs text-gray-500">
                        <x-heroicon-m-calendar class="h-3.5 w-3.5" />
                        {{ $post->published_at->format('d/m/Y') }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        @if ($this->posts->hasPages())
        <div class="mt-8">
            {{ $this->posts->links() }}
        </div>
        @endif
    </div>
</div>
