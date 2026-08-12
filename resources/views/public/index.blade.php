<x-layouts.public title="Blog">
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Blog</h1>
            <p class="mt-2 text-gray-500">As últimas publicações, direto ao ponto.</p>
        </div>

        <div class="space-y-6">
            @forelse ($posts as $post)
            <article class="group overflow-hidden rounded-xl border border-gray-200 transition hover:border-gray-300 hover:shadow-sm sm:flex">
                @if ($post->imageUrl())
                <a href="{{ route('posts.show', $post->slug) }}" class="block shrink-0 sm:w-56">
                    <img src="{{ $post->imageUrl() }}" alt="" class="h-48 w-full object-cover sm:h-full">
                </a>
                @endif

                <div class="flex flex-1 flex-col justify-center p-6">
                    <p class="flex flex-wrap items-center gap-x-1.5 text-xs text-gray-500">
                        <x-heroicon-m-calendar class="h-3.5 w-3.5" />
                        <span>{{ $post->published_at->format('d/m/Y') }}</span>
                        <span>&middot;</span>
                        <span>{{ $post->user->name }}</span>
                    </p>
                    <h2 class="mt-2 text-xl font-semibold text-gray-900">
                        <a href="{{ route('posts.show', $post->slug) }}" class="group-hover:text-indigo-600">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $post->excerpt() }}</p>
                    <a href="{{ route('posts.show', $post->slug) }}"
                        class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                        Ler mais
                        <x-heroicon-m-arrow-right class="h-4 w-4" />
                    </a>
                </div>
            </article>
            @empty
            <div class="rounded-xl border border-dashed border-gray-300 px-6 py-16 text-center">
                <x-heroicon-o-newspaper class="mx-auto h-10 w-10 text-gray-300" />
                <p class="mt-3 text-sm text-gray-500">Nenhum post publicado ainda.</p>
            </div>
            @endforelse
        </div>

        @if ($posts->hasPages())
        <div>
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</x-layouts.public>
