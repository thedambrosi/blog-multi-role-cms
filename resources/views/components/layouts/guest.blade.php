<!DOCTYPE html>
<html lang="pt-BR" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Blog' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="flex h-full min-h-screen flex-col items-center justify-center bg-gray-50 px-6 py-12 text-gray-900 antialiased">
    <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2 text-lg font-semibold text-gray-900">
        <x-heroicon-s-newspaper class="h-6 w-6 text-indigo-600" />
        Blog
    </a>

    <div class="w-full max-w-md">
        {{ $slot }}
    </div>

    @livewireScripts
</body>

</html>
