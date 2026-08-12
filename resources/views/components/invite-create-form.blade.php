<?php

use App\Models\Invite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component {
    public string $email = '';

    public ?string $inviteUrl = null;

    public function mount(): void
    {
        abort_unless(Auth::user()->role === 'admin', 403);
    }

    public function create(): void
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $invite = Invite::create([
            'token' => Str::random(40),
            'email' => $this->email,
            'expires_at' => now()->addHours(48),
            'created_by' => Auth::id(),
        ]);

        $this->inviteUrl = url("/convite/{$invite->token}");
        $this->email = '';
    }
}; ?>

<div>
    <h1 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
        <x-heroicon-o-user-plus class="h-5 w-5 text-indigo-600" />
        Convidar colaborador
    </h1>

    @if ($inviteUrl)
    <div class="mt-6">
        <div class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <x-heroicon-m-check-circle class="h-5 w-5 flex-shrink-0 text-green-500" />
            Convite criado! Copie o link e envie manualmente pra pessoa convidada.
        </div>

        <div class="mt-4 flex gap-2" x-data="{ copied: false }">
            <input type="text" readonly value="{{ $inviteUrl }}" x-ref="inviteLink"
                class="flex-1 rounded-lg border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-700">
            <button type="button"
                x-on:click="navigator.clipboard.writeText($refs.inviteLink.value); copied = true; setTimeout(() => copied = false, 2000)"
                class="flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                <x-heroicon-m-clipboard-document class="h-4 w-4" x-show="!copied" />
                <x-heroicon-m-check class="h-4 w-4" x-show="copied" x-cloak />
                <span x-show="!copied">Copiar</span>
                <span x-show="copied" x-cloak>Copiado!</span>
            </button>
        </div>

        <button type="button" wire:click="$set('inviteUrl', null)" class="mt-4 text-sm font-medium text-gray-500 hover:text-gray-900">
            Gerar outro convite
        </button>
    </div>
    @else
    <form wire:submit="create" class="mt-6 space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email do convidado</label>
            <input id="email" type="email" wire:model="email"
                class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('email')
            <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                {{ $message }}
            </p>
            @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="create"
            class="flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
            <span wire:loading.remove wire:target="create">Gerar convite</span>
            <span wire:loading wire:target="create">Gerando...</span>
        </button>
    </form>
    @endif
</div>
