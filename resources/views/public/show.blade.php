<x-layouts.public :title="$post->title">
    <article>
        @if ($post->imageUrl())
        <img src="{{ $post->imageUrl() }}" alt="" class="w-full rounded-md object-cover">
        @endif

        <h1 class="mt-6 text-2xl font-semibold text-gray-900">{{ $post->title }}</h1>
        <p class="mt-2 text-sm text-gray-500">
            {{ $post->user->name }} · {{ $post->published_at->format('d/m/Y') }}
        </p>

        <div class="mt-6 whitespace-pre-line text-gray-800">{{ $post->content }}</div>

        <a href="{{ route('home') }}" class="mt-10 inline-block text-sm text-gray-500 hover:text-gray-900">
            &larr; Voltar
        </a>
    </article>
</x-layouts.public>
