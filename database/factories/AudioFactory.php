<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AudioApproval;
use App\Enums\AudioType;
use App\Models\Audio;
use App\Models\TTSArtist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Audio>
 */
final class AudioFactory extends Factory
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
            'uuid' => fake()->uuid(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'type' => AudioType::TTS,
            'approval' => AudioApproval::Pending,
            'message' => fake()->randomElement([
                'নমস্কার, আশা করি আপনি ভালো আছেন। এটি একটি টেস্ট মেসেজ।',
                'হ্যালো! আপনার অর্ডারটি প্রসেসিং হচ্ছে।',
                'আপনার ভেরিফিকেশন কোড: '.fake()->numberBetween(100000, 999999),
                'আজকের অফার! '.fake()->numberBetween(10, 70).'% ছাড় চলছে।',
                'দুঃখিত, আপনার অনুরোধটি সম্পন্ন করা যায়নি। পরে আবার চেষ্টা করুন।',
                'আপনার টিকিট #'.fake()->numberBetween(1000, 9999).' গ্রহণ করা হয়েছে।',
                'আপনার পেমেন্ট সফল হয়েছে। Txn: '.mb_strtoupper(fake()->bothify('??###??###')),
                'সাপোর্টে যোগাযোগ করুন: support@'.fake()->freeEmailDomain(),
                'আপডেট: ডেলিভারি '.fake()->randomElement(['আজ', 'আগামীকাল', 'পরশু']).' হবে।',
            ]),
            'tts_artist_id' => TTSArtist::factory(),
        ];
    }
}
