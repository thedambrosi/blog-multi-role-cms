<?php

namespace Database\Factories;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invite>
 */
class InviteFactory extends Factory
{
    protected $model = Invite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => Str::random(40),
            'email' => fake()->unique()->safeEmail(),
            'used_at' => null,
            'expires_at' => now()->addHours(48),
            'created_by' => User::factory()->admin(),
        ];
    }

    /**
     * Indicate that the invite has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subHour(),
        ]);
    }

    /**
     * Indicate that the invite has already been used.
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_at' => now(),
        ]);
    }
}
