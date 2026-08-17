<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AudioApproval;
use App\Enums\AudioType;
use App\Jobs\ConvertAudioJob;
use App\Jobs\GenerateAudioJob;
use App\Models\Audio;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Bus;

final class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::find(2);

        $group = Group::factory()->create([
            'user_id' => $user->id,
            'name' => 'Demo Group',
        ]);

        $contacts = [
            ['phone_number' => '+8801322635808'],
            ['phone_number' => '+8801712345678'],
        ];

        foreach ($contacts as $contact) {
            $group->contacts()->create($contact);
        }

        Audio::factory()
            ->afterCreating(function (Audio $audio) {
                $audio->update([
                    'approval' => AudioApproval::Approved,
                ]);

                Bus::chain([
                    new GenerateAudioJob($audio->id),
                    new ConvertAudioJob($audio->id),
                ])->dispatch();
            })
            ->create([
                'user_id' => $user->id,
                'approval' => AudioApproval::Pending,
                'type' => AudioType::TTS,
                'tts_artist_id' => 85,
            ]);

        $user->callers()->sync([1]);
    }
}
