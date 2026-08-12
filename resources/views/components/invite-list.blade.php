<?php

use App\Models\Invite;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function invites()
    {
        return Invite::query()
            ->whereNull('used_at')
            ->latest()
            ->get();
    }
}; ?>

<div class="mt-4 space-y-3">
    @forelse ($this->invites as $invite)
    <div class="flex items-center justify-between rounded-xl border border-gray-200 px-5 py-4 {{ $invite->isExpired() ? 'bg-gray-50' : 'bg-white' }}">
        <div class="{{ $invite->isExpired() ? 'text-gray-400' : '' }}">
            <p class="flex items-center gap-2 text-sm font-medium {{ $invite->isExpired() ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                <x-heroicon-m-envelope class="h-4 w-4 flex-shrink-0" />
                {{ $invite->email }}
            </p>
            <p class="mt-1 text-xs {{ $invite->isExpired() ? 'text-gray-400' : 'text-gray-500' }}">
                {{ $invite->isExpired() ? 'expirou em' : 'expira em' }} {{ $invite->expires_at->format('d/m/Y \à\s H:i') }}
            </p>
        </div>

        <div x-data="{ copied: false }">
            <button type="button"
                x-on:click="navigator.clipboard.writeText('{{ url("/convite/{$invite->token}") }}'); copied = true; setTimeout(() => copied = false, 2000)"
                class="flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-gray-900">
                <x-heroicon-m-clipboard-document class="h-4 w-4" x-show="!copied" />
                <x-heroicon-m-check class="h-4 w-4 text-green-600" x-show="copied" x-cloak />
                <span x-show="!copied">Copiar link</span>
                <span x-show="copied" x-cloak class="text-green-600">Copiado!</span>
            </button>
        </div>
    </div>
    @empty
    <p class="mt-4 text-sm text-gray-500">Nenhum convite pendente.</p>
    @endforelse
</div>
