<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TTSEngine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TTSLanguage>
 */
final class TTSLanguageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->languageCode(),
            'code' => fake()->unique()->languageCode().'-'.fake()->countryCode(),
            'engine' => fake()->randomElement(TTSEngine::cases()),
            'enabled' => true,
        ];
    }
}
