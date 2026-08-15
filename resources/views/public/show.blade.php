<x-layouts.public :title="$post->title" :description="$post->excerpt(160)" :image="$post->imageUrl()">
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

    <section class="mt-14 border-t border-gray-200 pt-10">
        <h2 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
            <x-heroicon-o-chat-bubble-left-right class="h-5 w-5 text-indigo-600" />
            Comentários
        </h2>

        @if ($post->comments->isEmpty())
        <p class="mt-4 text-sm text-gray-500">Nenhum comentário ainda. Seja o primeiro a comentar.</p>
        @else
        <div class="mt-6 space-y-6">
            @foreach ($post->comments as $comment)
            <div wire:key="comment-{{ $comment->id }}" class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-sm font-medium text-gray-900">{{ $comment->name }}</p>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $comment->body }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <div class="mt-8">
            <h3 class="text-sm font-semibold text-gray-900">Deixe seu comentário</h3>
            <div class="mt-4">
                <livewire:comment-form :post="$post" />
            </div>
        </div>
    </section>
</x-layouts.public>
