<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class NoProfanity implements ValidationRule
{
    /**
     * Palavrões/xingamentos comuns em português (sem acento, minúsculo).
     * Lista não exaustiva — pode ser ampliada depois.
     *
     * @var array<int, string>
     */
    protected array $blocklist = [
        'arrombado',
        'arrombada',
        'babaca',
        'bosta',
        'burro',
        'burra',
        'canalha',
        'caralho',
        'corno',
        'cretino',
        'cretina',
        'desgraca',
        'desgracado',
        'filho da puta',
        'foda',
        'fudido',
        'idiota',
        'imbecil',
        'merda',
        'otario',
        'otaria',
        'porra',
        'puta',
        'putaria',
        'retardado',
        'retardada',
        'safado',
        'safada',
        'vagabundo',
        'vagabunda',
        'viado',
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = Str::of((string) $value)->lower()->ascii()->toString();

        foreach ($this->blocklist as $word) {
            if (preg_match('/\b'.preg_quote($word, '/').'\b/u', $normalized) === 1) {
                $fail('Seu comentário contém linguagem imprópria. Por favor, revise o texto.');

                return;
            }
        }
    }
}
