<?php

use App\Models\User;
use Illuminate\Support\Str;
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

<div class="mt-8 space-y-3">
    @forelse ($this->users as $user)
    <div wire:key="user-{{ $user->id }}"
        class="flex flex-wrap items-center gap-4 rounded-xl border border-gray-200 px-5 py-4 {{ $user->is_active ? 'bg-white' : 'bg-gray-50' }}">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full text-sm font-semibold {{ $user->is_active ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-400' }}">
            {{ Str::of($user->name)->substr(0, 1)->upper() }}
        </div>

        <div class="min-w-0 flex-1 {{ $user->is_active ? '' : 'text-gray-400' }}">
            <p class="flex flex-wrap items-center gap-2 text-sm font-medium {{ $user->is_active ? 'text-gray-900' : 'text-gray-400' }}">
                {{ $user->name }}

                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                    {{ $user->role === 'admin' ? 'Admin' : 'Colaborador' }}
                </span>

                @unless ($user->is_active)
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-600">
                    <x-heroicon-m-no-symbol class="h-3 w-3" />
                    acesso removido
                </span>
                @endunless
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ $user->email }} &middot; desde {{ $user->created_at->format('d/m/Y') }}
            </p>
        </div>

        @if ($user->is_active && $user->id !== auth()->id())
        <button type="button" wire:click="removeAccess({{ $user->id }})"
            wire:confirm="Remover o acesso de {{ $user->name }}? Essa ação é definitiva."
            wire:loading.attr="disabled" wire:target="removeAccess({{ $user->id }})"
            class="flex items-center gap-1.5 text-sm text-red-500 hover:text-red-700 disabled:opacity-50">
            <x-heroicon-m-user-minus class="h-4 w-4" />
            Remover acesso
        </button>
        @endif
    </div>
    @empty
    <p class="text-sm text-gray-500">Nenhum usuário cadastrado.</p>
    @endforelse
</div>
