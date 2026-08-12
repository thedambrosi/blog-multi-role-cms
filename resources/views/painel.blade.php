<x-layouts.app title="Painel">
    <div class="flex h-full min-h-screen items-center justify-center px-6">
        <div class="max-w-md text-center">
            <h1 class="text-2xl font-semibold">Bem-vindo, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-sm text-gray-500">O painel completo ainda está em construção.</p>
            <a href="{{ route('painel.posts.index') }}" class="mt-6 inline-block text-sm font-medium text-gray-900 underline">
                Ver meus posts
            </a>
        </div>
    </div>
</x-layouts.app>
