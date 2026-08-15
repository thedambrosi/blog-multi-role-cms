<?php

use App\Rules\NoProfanity;
use Illuminate\Support\Facades\Validator;

test('texto limpo passa na validação', function () {
    $validator = Validator::make(
        ['body' => 'Adorei o post, muito bem explicado!'],
        ['body' => [new NoProfanity]]
    );

    expect($validator->fails())->toBeFalse();
});

test('texto com palavrão falha na validação', function () {
    $validator = Validator::make(
        ['body' => 'Esse post é uma bosta'],
        ['body' => [new NoProfanity]]
    );

    expect($validator->fails())->toBeTrue();
});

test('palavrão com acentuação diferente ainda é bloqueado', function () {
    $validator = Validator::make(
        ['body' => 'Que comentário otário, hein'],
        ['body' => [new NoProfanity]]
    );

    expect($validator->fails())->toBeTrue();
});

test('palavra legítima que contém a sequência de letras de um palavrão não é bloqueada', function () {
    // "computador" contém "puta" como substring, mas não é a palavra inteira.
    $validator = Validator::make(
        ['body' => 'Escrevi esse comentário direto do meu computador'],
        ['body' => [new NoProfanity]]
    );

    expect($validator->fails())->toBeFalse();
});
