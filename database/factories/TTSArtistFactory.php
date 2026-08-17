<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TTSArtist>
 */
final class TTSArtistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tts_language_id' => \App\Models\TTSLanguage::factory(),
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement([\App\Enums\TTSArtistGender::Male, \App\Enums\TTSArtistGender::Female]),
            'code' => $this->faker->unique()->slug(),
            'enabled' => true,
        ];
    }
}
