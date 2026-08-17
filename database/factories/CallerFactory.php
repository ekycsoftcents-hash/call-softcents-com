<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Caller;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Caller>
 */
final class CallerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'server_id' => Server::factory(),
            'caller_name' => fake()->name(),
            'caller_number' => $this->faker->e164PhoneNumber(),
            'trunk_name' => $this->faker->word(),
            'enabled' => fake()->boolean(),
        ];
    }
}
