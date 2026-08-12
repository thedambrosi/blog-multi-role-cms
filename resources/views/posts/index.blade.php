<x-layouts.app title="Posts">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <x-flash-messages />

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $scope === 'all' ? 'Todos os Posts' : 'Meus Posts' }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $scope === 'all' ? 'Posts de todos os colaboradores.' : 'Os posts que você escreveu.' }}
                </p>
            </div>
            <a href="{{ route(($scope === 'all' ? 'admin' : 'painel').'.posts.create') }}"
                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <x-heroicon-m-plus class="h-4 w-4" />
                Novo post
            </a>
        </div>

        <livewire:post-list :scope="$scope" />
    </div>
</x-layouts.app>
