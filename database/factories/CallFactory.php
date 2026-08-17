<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CallStatus;
use App\Enums\CallType;
use App\Models\Call;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Call>
 */
final class CallFactory extends Factory
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
            'campaign_id' => Campaign::factory(),
            'contact_id' => Contact::factory(),
            'type' => fake()->randomElement(CallType::cases()),
            'phone_number' => '+88017'.fake()->numerify('#########'),
            'content' => fake()->paragraph(),
            'duration' => fake()->numberBetween(30, 3600), // duration in seconds
            'cost' => fake()->randomFloat(2, 0.5, 10.0), // cost in dollars
            'status' => fake()->randomElement(CallStatus::cases()),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
