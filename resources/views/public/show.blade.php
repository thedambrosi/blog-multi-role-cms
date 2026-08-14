<x-layouts.public :title="$post->title">
    <article>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900">
            <x-heroicon-m-arrow-left class="h-4 w-4" />
            Voltar
        </a>

        <h1 class="mt-6 text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl">{{ $post->title }}</h1>

        <p class="mt-4 flex items-center gap-1.5 text-sm text-gray-500">
            <x-heroicon-m-user class="h-4 w-4" />
            <span>{{ $post->user->name }}</span>
            <span>&middot;</span>
            <x-heroicon-m-calendar class="h-4 w-4" />
            <span>{{ $post->published_at->format('d/m/Y') }}</span>
        </p>

        @if ($post->categories->isNotEmpty())
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($post->categories as $category)
            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">{{ $category->name }}</span>
            @endforeach
        </div>
        @endif

        @if ($post->imageUrl())
        <img src="{{ $post->imageUrl() }}" alt="" class="mt-8 w-full rounded-xl object-cover">
        @endif

        <div class="mt-8 max-w-none text-[17px] leading-relaxed text-gray-800">
            @foreach (explode("\n\n", trim($post->content)) as $paragraph)
            @continue(trim($paragraph) === '')
            <p class="mb-5 whitespace-pre-line">{{ trim($paragraph) }}</p>
            @endforeach
        </div>

        <a href="{{ route('home') }}"
            class="mt-10 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-700">
            <x-heroicon-m-arrow-left class="h-4 w-4" />
            Ver todos os posts
        </a>
    </article>
</x-layouts.public>
