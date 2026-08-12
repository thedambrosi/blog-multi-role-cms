<x-layouts.public title="Blog">
    <div class="space-y-10">
        @forelse ($posts as $post)
        <article class="flex gap-6">
            @if ($post->imageUrl())
            <img src="{{ $post->imageUrl() }}" alt="" class="h-32 w-32 flex-shrink-0 rounded-md object-cover">
            @endif

            <div>
                <p class="text-xs text-gray-500">
                    {{ $post->user->name }} · {{ $post->published_at->format('d/m/Y') }}
                </p>
                <h2 class="mt-1 text-lg font-semibold text-gray-900">
                    <a href="{{ route('posts.show', $post->slug) }}" class="hover:underline">
                        {{ $post->title }}
                    </a>
                </h2>
                <p class="mt-2 text-sm text-gray-600">{{ $post->excerpt() }}</p>
            </div>
        </article>
        @empty
        <p class="text-sm text-gray-500">Nenhum post publicado ainda.</p>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $posts->links() }}
    </div>
</x-layouts.public>
