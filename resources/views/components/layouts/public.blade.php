<!DOCTYPE html>
<html lang="pt-BR" class="h-full">

@php
    $metaDescription = $description ?? 'As últimas publicações do blog.';
    $metaTitle = $title ?? 'Blog';
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if (!empty($image))
    <meta property="og:image" content="{{ $image }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex h-full min-h-screen flex-col bg-white text-gray-900 antialiased">
    <header class="border-b border-gray-200">
        <div class="mx-auto flex max-w-3xl items-center px-6 py-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                <x-heroicon-s-newspaper class="h-6 w-6 text-indigo-600" />
                Blog
            </a>
        </div>
    </header>

    <main class="mx-auto w-full max-w-3xl flex-1 px-6 py-10 sm:py-14">
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-200">
        <div class="mx-auto max-w-3xl px-6 py-8 text-sm text-gray-500">
            &copy; {{ now()->year }} Blog. Todos os direitos reservados.
        </div>
    </footer>
</body>

</html>
