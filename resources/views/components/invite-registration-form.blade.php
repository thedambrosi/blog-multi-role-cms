<?php

use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
        $throttleKey = 'invite-register:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->addError('name', "Muitas tentativas. Tente novamente em {$seconds} segundos.");

            return;
        }

        RateLimiter::hit($throttleKey, 300);

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

        $this->redirect(route($user->homeRouteName()));
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
                class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('name')
            <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                {{ $message }}
            </p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
            <input id="password" type="password" wire:model="password"
                class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('password')
            <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                {{ $message }}
            </p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar senha</label>
            <input id="password_confirmation" type="password" wire:model="password_confirmation"
                class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        <button type="submit" wire:loading.attr="disabled"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
            <span wire:loading.remove wire:target="register">Criar conta</span>
            <span wire:loading wire:target="register">Criando conta...</span>
        </button>
    </form>
</div>
