<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Define o slug manualmente (via forceFill) em vez de depender do evento
     * "creating" do model: o DatabaseSeeder usa WithoutModelEvents, que
     * desativa eventos do Eloquent durante o seed inteiro.
     */
    public function run(): void
    {
        collect(['Notícias', 'Tutoriais', 'Opinião', 'Bastidores'])->each(function (string $name) {
            $slug = Str::slug($name);

            Category::query()->firstOrNew(['slug' => $slug])
                ->forceFill(['name' => $name, 'slug' => $slug])
                ->save();
        });
    }
}
