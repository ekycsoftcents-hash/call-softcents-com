<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Models\Audio;
use App\Models\Caller;
use App\Models\Campaign;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
final class CampaignFactory extends Factory
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
            'audio_id' => Audio::factory(),
            'caller_id' => Caller::factory(),
            'group_id' => Group::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->text(),
            'status' => CampaignStatus::Pending,
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
