<x-layouts.app title="Admin">
    <div class="mx-auto max-w-5xl px-6 py-10">
        <x-flash-messages />

        <h1 class="text-2xl font-semibold text-gray-900">Olá, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-gray-500">Painel de administração do blog.</p>

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <a href="{{ route('admin.posts.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-indigo-300 hover:shadow-sm">
                <x-heroicon-o-document-text class="h-8 w-8 text-indigo-600" />
                <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-indigo-600">Todos os Posts</h2>
                <p class="mt-1 text-sm text-gray-500">Veja e gerencie os posts de todo mundo.</p>
            </a>

            <a href="{{ route('admin.users.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-indigo-300 hover:shadow-sm">
                <x-heroicon-o-users class="h-8 w-8 text-indigo-600" />
                <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-indigo-600">Usuários</h2>
                <p class="mt-1 text-sm text-gray-500">Gerencie o acesso de admins e colaboradores.</p>
            </a>

            <a href="{{ route('admin.invites.create') }}"
                class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-indigo-300 hover:shadow-sm">
                <x-heroicon-o-user-plus class="h-8 w-8 text-indigo-600" />
                <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-indigo-600">Convidar colaborador</h2>
                <p class="mt-1 text-sm text-gray-500">Gere um novo link de convite.</p>
            </a>
        </div>
    </div>
</x-layouts.app>
