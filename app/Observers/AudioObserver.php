<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\AudioApproval;
use App\Enums\AudioTTSStatus;
use App\Enums\AudioType;
use App\Filament\Admin\Resources\Calling\Audio\AudioResource;
use App\Jobs\ConvertAudioJob;
use App\Jobs\GenerateAudioJob;
use App\Models\Audio;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

final class AudioObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Audio $audio): void
    {
        $audio->loadMissing('user');

        if ($audio->user->auto_approve_audio) {
            $this->autoApproveAndProcess($audio);
        } else {
            $this->notifyAdmins($audio);
        }
    }

    public function updated(Audio $audio): void
    {
        if ($this->shouldProcessAudio($audio)) {
            $this->dispatchAudioProcessingJobs($audio);
        }
    }

    private function notifyAdmins(Audio $audio): void
    {
        $admins = Cache::remember(
            'users:admins',
            now()->addMinutes(15),
            fn () => User::admin()->get()
        );

        Notification::make()
            ->title('New Audio Created')
            ->body("Audio has been created by {$audio->user->name}")
            ->success()
            ->actions([
                Action::make('view')
                    ->label('View Audio')
                    ->url(AudioResource::getUrl('view', ['record' => $audio->id], panel: 'admin')),
            ])
            ->sendToDatabase($admins);
    }

    private function autoApproveAndProcess(Audio $audio): void
    {
        $audio->updateQuietly(['approval' => AudioApproval::Approved]);
        $this->dispatchAudioProcessingJobs($audio);
    }

    private function shouldProcessAudio(Audio $audio): bool
    {
        return $audio->wasChanged('approval')
            && $audio->approval === AudioApproval::Approved;
    }

    private function dispatchAudioProcessingJobs(Audio $audio): void
    {
        match ($audio->type) {
            AudioType::TTS => $this->processTTSAudio($audio),
            AudioType::Upload => $this->processUploadedAudio($audio),
            default => null,
        };
    }

    private function processTTSAudio(Audio $audio): void
    {
        if ($audio->tts_status === AudioTTSStatus::Completed) {
            return;
        }

        Bus::chain([
            new GenerateAudioJob($audio->id),
            new ConvertAudioJob($audio->id),
        ])->dispatch();
    }

    private function processUploadedAudio(Audio $audio): void
    {
        if ($audio->conversion_status === AudioTTSStatus::Completed) {
            return;
        }

        ConvertAudioJob::dispatch($audio->id);
    }
}
