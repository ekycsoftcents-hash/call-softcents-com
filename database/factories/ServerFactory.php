<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Server>
 */
final class ServerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'ari_scheme' => fake()->randomElement(['http', 'https']),
            'ari_host' => fake()->ipv4(),
            'ari_port' => 8088,
            'ari_username' => fake()->userName(),
            'ari_password' => fake()->password(),
            'database_host' => fake()->ipv4(),
            'database_port' => 3306,
            'database_username' => fake()->userName(),
            'database_password' => fake()->password(),
            'enabled' => fake()->boolean(),
        ];
    }
}
