<x-layouts.app title="{{ $post ? 'Editar post' : 'Novo post' }}">
    <div class="mx-auto max-w-2xl px-6 py-10">
        <livewire:post-form :post="$post" :scope="$scope" />
    </div>
</x-layouts.app>
