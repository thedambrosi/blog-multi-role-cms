<?php

use App\Models\Comment;
use App\Models\Post;
use App\Rules\NoProfanity;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public ?Post $post = null;

    public string $name = '';

    public string $email = '';

    public string $body = '';

    public bool $submitted = false;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function submit(): void
    {
        $throttleKey = 'comment-create:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->addError('body', "Muitos comentários enviados. Tente novamente em {$seconds} segundos.");

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'min:3', 'max:2000', new NoProfanity],
        ]);

        Comment::create([
            'post_id' => $this->post->id,
            'name' => $this->name,
            'email' => $this->email,
            'body' => $this->body,
        ]);

        $this->reset(['name', 'email', 'body']);
        $this->submitted = true;
    }
}; ?>

<div>
    @if ($submitted)
    <div class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        <x-heroicon-m-check-circle class="h-5 w-5 flex-shrink-0 text-green-500" />
        Comentário enviado! Ele vai aparecer assim que for aprovado.
    </div>

    <button type="button" wire:click="$set('submitted', false)" class="mt-4 text-sm font-medium text-gray-500 hover:text-gray-900">
        Deixar outro comentário
    </button>
    @else
    <form wire:submit="submit" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="comment-name" class="block text-sm font-medium text-gray-700">Nome</label>
                <input id="comment-name" type="text" wire:model="name"
                    class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('name')
                <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                    <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div>
                <label for="comment-email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="comment-email" type="email" wire:model="email"
                    class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('email')
                <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                    <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                    {{ $message }}
                </p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-500">Seu email não será exibido publicamente.</p>
            </div>
        </div>

        <div>
            <label for="comment-body" class="block text-sm font-medium text-gray-700">Comentário</label>
            <textarea id="comment-body" wire:model="body" rows="4"
                class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm leading-relaxed shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
            @error('body')
            <p class="mt-1.5 flex items-center gap-1 text-sm text-red-600">
                <x-heroicon-m-exclamation-circle class="h-4 w-4 flex-shrink-0" />
                {{ $message }}
            </p>
            @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="submit"
            class="flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
            <span wire:loading.remove wire:target="submit">Enviar comentário</span>
            <span wire:loading wire:target="submit">Enviando...</span>
        </button>
    </form>
    @endif
</div>
