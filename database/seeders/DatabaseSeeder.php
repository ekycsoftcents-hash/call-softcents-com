<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserAudioType;
use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TTSLanguageSeeder::class,
            TTSArtistSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'type' => UserType::Admin,
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@mail.com',
            'type' => UserType::User,
            'email_verified_at' => now(),
            'balance' => 500,
            'audio_type' => UserAudioType::Both,
            'pulse_rate' => 0.50,
            'pulse_duration' => 30,
            'status' => UserStatus::Approved,
            'auto_approve_audio' => true,
            'auto_approve_campaigns' => true,
        ]);

        $this->call([
            ServerSeeder::class,
            CallerSeeder::class,
            // DemoSeeder::class,
        ]);
    }
}
