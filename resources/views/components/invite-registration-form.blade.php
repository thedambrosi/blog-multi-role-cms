<?php

use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public string $token = '';

    public string $name = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
    }

    public function register(): void
    {
        $invite = Invite::where('token', $this->token)->first();

        abort_if(! $invite || $invite->isExpired() || $invite->isUsed(), 404);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $invite->email,
            'password' => $this->password,
        ]);

        $invite->forceFill(['used_at' => now()])->save();

        Auth::login($user);

        session()->regenerate();

        $this->redirect(route('painel'));
    }
}; ?>

<div>
    <h1 class="text-xl font-semibold text-gray-900">Criar conta</h1>
    <p class="mt-1 text-sm text-gray-500">
        Você foi convidado para colaborar. Defina seu nome e uma senha para continuar.
    </p>

    <form wire:submit="register" class="mt-6 space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
            <input id="name" type="text" wire:model="name"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-500 focus:outline-none">
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
            <input id="password" type="password" wire:model="password"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-500 focus:outline-none">
            @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar senha</label>
            <input id="password_confirmation" type="password" wire:model="password_confirmation"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-500 focus:outline-none">
        </div>

        <button type="submit" wire:loading.attr="disabled"
            class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 disabled:opacity-50">
            Criar conta
        </button>
    </form>
</div>
