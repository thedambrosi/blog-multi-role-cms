@if (session('success'))
<div class="mb-6 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
    <x-heroicon-m-check-circle class="h-5 w-5 flex-shrink-0 text-green-500" />
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="mb-6 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
    <x-heroicon-m-exclamation-circle class="h-5 w-5 flex-shrink-0 text-red-500" />
    {{ session('error') }}
</div>
@endif
