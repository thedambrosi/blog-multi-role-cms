<?php

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public string $scope = 'own';

    #[Computed]
    public function comments()
    {
        return Comment::query()
            ->with('post')
            ->when($this->scope !== 'all', fn ($query) => $query->whereHas('post', fn ($query) => $query->where('user_id', Auth::id())))
            ->latest()
            ->get();
    }

    public function approve(Comment $comment): void
    {
        $this->authorize('update', $comment);

        $comment->forceFill(['status' => 'approved'])->save();

        unset($this->comments);
    }

    public function reject(Comment $comment): void
    {
        $this->authorize('update', $comment);

        $comment->forceFill(['status' => 'rejected'])->save();

        unset($this->comments);
    }

    public function delete(Comment $comment): void
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        unset($this->comments);
    }
}; ?>

<div class="mt-8 space-y-3">
    @forelse ($this->comments as $comment)
    <div wire:key="comment-{{ $comment->id }}" class="rounded-xl border border-gray-200 bg-white px-5 py-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                    @if ($comment->status === 'approved')
                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 font-medium text-green-700">
                        <x-heroicon-m-check-circle class="h-3.5 w-3.5" />
                        Aprovado
                    </span>
                    @elseif ($comment->status === 'rejected')
                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 font-medium text-red-700">
                        <x-heroicon-m-x-circle class="h-3.5 w-3.5" />
                        Rejeitado
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 font-medium text-amber-700">
                        <x-heroicon-m-clock class="h-3.5 w-3.5" />
                        Pendente
                    </span>
                    @endif

                    <span>&middot;</span>
                    <span class="flex items-center gap-1">
                        <x-heroicon-m-document-text class="h-3.5 w-3.5" />
                        {{ $comment->post->title }}
                    </span>
                </div>

                <p class="mt-2 text-sm font-medium text-gray-900">{{ $comment->name }}</p>
                <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ $comment->body }}</p>
            </div>

            <div class="flex flex-shrink-0 items-center gap-4 text-sm">
                @can('update', $comment)
                @if ($comment->status !== 'approved')
                <button type="button" wire:click="approve({{ $comment->id }})"
                    wire:loading.attr="disabled" wire:target="approve({{ $comment->id }})"
                    class="flex items-center gap-1.5 text-green-600 hover:text-green-800 disabled:opacity-50">
                    <x-heroicon-m-check class="h-4 w-4" />
                    Aprovar
                </button>
                @endif
                @if ($comment->status !== 'rejected')
                <button type="button" wire:click="reject({{ $comment->id }})"
                    wire:loading.attr="disabled" wire:target="reject({{ $comment->id }})"
                    class="flex items-center gap-1.5 text-gray-500 hover:text-gray-900 disabled:opacity-50">
                    <x-heroicon-m-x-mark class="h-4 w-4" />
                    Rejeitar
                </button>
                @endif
                @endcan
                @can('delete', $comment)
                <button type="button" wire:click="delete({{ $comment->id }})" wire:confirm="Excluir este comentário? Essa ação não pode ser desfeita."
                    wire:loading.attr="disabled" wire:target="delete({{ $comment->id }})"
                    class="flex items-center gap-1.5 text-red-500 hover:text-red-700 disabled:opacity-50">
                    <x-heroicon-m-trash class="h-4 w-4" />
                    Excluir
                </button>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="rounded-xl border border-dashed border-gray-300 px-6 py-16 text-center">
        <x-heroicon-o-chat-bubble-left-right class="mx-auto h-10 w-10 text-gray-300" />
        <p class="mt-3 text-sm text-gray-500">Nenhum comentário por aqui ainda.</p>
    </div>
    @endforelse
</div>
