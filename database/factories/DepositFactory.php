<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deposit>
 */
final class DepositFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'BDT',
            'payment_method' => 'piprapay',
            'status' => 'pending',
            'transaction_id' => $this->faker->uuid(),
            'meta' => [],
        ];
    }
}
