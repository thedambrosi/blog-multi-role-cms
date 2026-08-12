<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function users()
    {
        return User::query()->latest()->get();
    }

    public function removeAccess(User $user): void
    {
        $this->authorize('removeAccess', $user);

        $user->forceFill(['is_active' => false])->save();

        unset($this->users);
    }
}; ?>

<div class="mt-8 space-y-4">
    @forelse ($this->users as $user)
    <div class="flex items-center justify-between rounded-md border border-gray-200 px-4 py-3 {{ $user->is_active ? '' : 'bg-gray-50' }}">
        <div class="{{ $user->is_active ? '' : 'text-gray-400' }}">
            <p class="text-sm font-medium">
                {{ $user->name }}
                @unless ($user->is_active)
                <span class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-600">acesso removido</span>
                @endunless
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ $user->email }} · {{ $user->role }} · desde {{ $user->created_at->format('d/m/Y') }}
            </p>
        </div>

        @if ($user->is_active && $user->id !== auth()->id())
        <button type="button" wire:click="removeAccess({{ $user->id }})" wire:confirm="Remover o acesso de {{ $user->name }}? Essa ação é definitiva."
            class="text-sm text-red-600 hover:text-red-800">
            Remover acesso
        </button>
        @endif
    </div>
    @empty
    <p class="text-sm text-gray-500">Nenhum usuário cadastrado.</p>
    @endforelse
</div>
