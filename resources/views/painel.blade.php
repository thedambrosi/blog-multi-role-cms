<x-layouts.app title="Painel">
    <div class="mx-auto max-w-5xl px-6 py-10">
        <x-flash-messages />

        <h1 class="text-2xl font-semibold text-gray-900">Olá, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-gray-500">Bem-vindo(a) de volta ao seu painel.</p>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <a href="{{ route('painel.posts.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-indigo-300 hover:shadow-sm">
                <x-heroicon-o-document-text class="h-8 w-8 text-indigo-600" />
                <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-indigo-600">Meus Posts</h2>
                <p class="mt-1 text-sm text-gray-500">Veja e gerencie tudo que você publicou.</p>
            </a>

            <a href="{{ route('painel.posts.create') }}"
                class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-indigo-300 hover:shadow-sm">
                <x-heroicon-o-plus-circle class="h-8 w-8 text-indigo-600" />
                <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-indigo-600">Novo Post</h2>
                <p class="mt-1 text-sm text-gray-500">Comece a escrever um novo conteúdo.</p>
            </a>
        </div>
    </div>
</x-layouts.app>
