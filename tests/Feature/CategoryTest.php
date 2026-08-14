<?php

use App\Models\Category;

test('slug é gerado a partir do nome da categoria', function () {
    $category = Category::factory()->create(['name' => 'Notícias Curiosas']);

    expect($category->slug)->toBe('noticias-curiosas');
});

test('slugs duplicados recebem um sufixo incremental', function () {
    Category::factory()->create(['name' => 'Tutoriais']);
    $second = Category::factory()->create(['name' => 'Tutoriais']);

    expect($second->slug)->toBe('tutoriais-1');
});
