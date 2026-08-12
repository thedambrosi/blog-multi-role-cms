<x-layouts.guest title="Entrar">
    <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-900">Entrar</h1>
        <p class="mt-1 text-sm text-gray-500">Acesso restrito à equipe do blog.</p>

        <x-flash-messages />

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4" x-data="{ loading: false }" x-on:submit="loading = true">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('email')
                <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                    <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input id="password" type="password" name="password" required
                    class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('password')
                <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                    <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                    {{ $message }}
                </p>
                @enderror
            </div>

            <button type="submit" x-bind:disabled="loading"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                <span x-show="!loading">Entrar</span>
                <span x-show="loading" x-cloak>Entrando...</span>
            </button>
        </form>
    </div>
</x-layouts.guest>
