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
    <div class="flex items-center justify-between rounded-md border border-gray-200 px-4 py-3 {{ $invite->isExpired() ? 'text-gray-400 line-through' : '' }}">
        <div>
            <p class="text-sm font-medium">{{ $invite->email }}</p>
            <p class="mt-1 text-xs {{ $invite->isExpired() ? 'text-gray-400' : 'text-gray-500' }}">
                {{ $invite->isExpired() ? 'expirou em' : 'expira em' }} {{ $invite->expires_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <button type="button"
            x-data
            x-on:click="navigator.clipboard.writeText('{{ url("/convite/{$invite->token}") }}')"
            class="text-sm text-gray-600 hover:text-gray-900">
            Copiar link
        </button>
    </div>
    @empty
    <p class="text-sm text-gray-500">Nenhum convite pendente.</p>
    @endforelse
</div>
