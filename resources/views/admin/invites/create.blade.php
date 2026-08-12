<x-layouts.app title="Convidar colaborador">
    <div class="mx-auto max-w-md px-6 py-10">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900">
            <x-heroicon-m-arrow-left class="h-4 w-4" />
            Voltar pra usuários
        </a>

        <div class="mt-4 rounded-xl border border-gray-200 bg-white p-6 sm:p-8">
            <livewire:invite-create-form />
        </div>
    </div>
</x-layouts.app>
