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
    <h1 class="text-xl font-semibold text-gray-900">Convidar colaborador</h1>

    @if ($inviteUrl)
    <div class="mt-6">
        <p class="text-sm text-gray-600">Convite criado! Copie o link e envie manualmente pra pessoa convidada:</p>

        <div class="mt-3 flex gap-2" x-data="{ copied: false }">
            <input type="text" readonly value="{{ $inviteUrl }}" x-ref="inviteLink"
                class="flex-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
            <button type="button"
                x-on:click="navigator.clipboard.writeText($refs.inviteLink.value); copied = true; setTimeout(() => copied = false, 2000)"
                class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                <span x-show="!copied">Copiar link</span>
                <span x-show="copied" x-cloak>Copiado!</span>
            </button>
        </div>

        <button type="button" wire:click="$set('inviteUrl', null)" class="mt-4 text-sm text-gray-500 hover:text-gray-900">
            Gerar outro convite
        </button>
    </div>
    @else
    <form wire:submit="create" class="mt-6 space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email do convidado</label>
            <input id="email" type="email" wire:model="email"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-500 focus:outline-none">
            @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled"
            class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 disabled:opacity-50">
            Gerar convite
        </button>
    </form>
    @endif
</div>
