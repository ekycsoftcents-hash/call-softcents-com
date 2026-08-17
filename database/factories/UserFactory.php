<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->email(),
            'type' => UserType::User,
            'phone' => '+88017'.fake()->numerify('#######'),
            'balance' => fake()->randomFloat(2, 0, 1000),
            'pulse_rate' => fake()->randomFloat(2, 0.10, 1.0),
            'pulse_duration' => fake()->randomElement([10, 30, 60]),

            'password' => self::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),

            'email_verified_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
