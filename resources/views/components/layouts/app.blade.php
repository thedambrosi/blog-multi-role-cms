<!DOCTYPE html>
<html lang="pt-BR" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Blog' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gray-50 text-gray-900 antialiased">
    <div class="flex min-h-full flex-col">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-6 py-4">
                <a href="{{ route(auth()->user()->homeRouteName()) }}" class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                    <x-heroicon-s-newspaper class="h-6 w-6 text-indigo-600" />
                    Blog
                </a>

                <nav class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm font-medium text-gray-600">
                    @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.posts.index') }}" class="flex items-center gap-1.5 hover:text-gray-900 {{ request()->routeIs('admin.posts.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon-m-document-text class="h-4 w-4" />
                        Todos os Posts
                    </a>
                    <a href="{{ route('admin.posts.create') }}" class="flex items-center gap-1.5 hover:text-gray-900 {{ request()->routeIs('admin.posts.create') ? 'text-indigo-600' : '' }}">
                        <x-heroicon-m-plus class="h-4 w-4" />
                        Novo Post
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-1.5 hover:text-gray-900 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon-m-users class="h-4 w-4" />
                        Usuários
                    </a>
                    <a href="{{ route('admin.invites.create') }}" class="flex items-center gap-1.5 hover:text-gray-900 {{ request()->routeIs('admin.invites.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon-m-user-plus class="h-4 w-4" />
                        Convites
                    </a>
                    @else
                    <a href="{{ route('painel.posts.index') }}" class="flex items-center gap-1.5 hover:text-gray-900 {{ request()->routeIs('painel.posts.index') ? 'text-indigo-600' : '' }}">
                        <x-heroicon-m-document-text class="h-4 w-4" />
                        Meus Posts
                    </a>
                    <a href="{{ route('painel.posts.create') }}" class="flex items-center gap-1.5 hover:text-gray-900 {{ request()->routeIs('painel.posts.create') ? 'text-indigo-600' : '' }}">
                        <x-heroicon-m-plus class="h-4 w-4" />
                        Novo Post
                    </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1.5 text-gray-600 hover:text-gray-900">
                            <x-heroicon-m-arrow-right-on-rectangle class="h-4 w-4" />
                            Sair
                        </button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
