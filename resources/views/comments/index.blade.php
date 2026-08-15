<x-layouts.app title="Comentários">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <x-flash-messages />

        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $scope === 'all' ? 'Todos os Comentários' : 'Meus Comentários' }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $scope === 'all' ? 'Comentários de todos os posts.' : 'Comentários nos posts que você escreveu.' }}
            </p>
        </div>

        <livewire:comment-list :scope="$scope" />
    </div>
</x-layouts.app>
