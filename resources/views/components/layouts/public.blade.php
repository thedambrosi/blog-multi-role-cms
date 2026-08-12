<!DOCTYPE html>
<html lang="pt-BR" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Blog' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-white text-gray-900 antialiased">
    <header class="border-b border-gray-200">
        <div class="mx-auto max-w-3xl px-6 py-6">
            <a href="{{ route('home') }}" class="text-lg font-semibold text-gray-900">Blog</a>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-6 py-10">
        {{ $slot }}
    </main>
</body>

</html>
