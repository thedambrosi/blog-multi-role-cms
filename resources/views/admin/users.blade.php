<x-layouts.app title="Usuários">
    <div class="mx-auto max-w-3xl px-6 py-10">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Usuários</h1>
            <a href="{{ route('admin.invites.create') }}"
                class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">
                Convidar colaborador
            </a>
        </div>

        <livewire:user-list />

        <div class="mt-12">
            <h2 class="text-lg font-semibold text-gray-900">Convites pendentes</h2>
            <livewire:invite-list />
        </div>
    </div>
</x-layouts.app>
