<x-layouts.app title="Usuários">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <x-flash-messages />

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Usuários</h1>
                <p class="mt-1 text-sm text-gray-500">Admins e colaboradores com acesso ao painel.</p>
            </div>
            <a href="{{ route('admin.invites.create') }}"
                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <x-heroicon-m-user-plus class="h-4 w-4" />
                Convidar colaborador
            </a>
        </div>

        <livewire:user-list />

        <div class="mt-12">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5 text-indigo-600" />
                Convites pendentes
            </h2>
            <livewire:invite-list />
        </div>
    </div>
</x-layouts.app>
