<?php

declare(strict_types=1);

namespace App\Console\Commands\Campaign;

use App\Enums\CallStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Settings\CallingSetting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('campaign:finish')]
#[Description('Complete campaigns where all calls have reached a terminal state')]
final class Complete extends Command
{
    public function handle(CallingSetting $callingSetting): void
    {
        $activeStates = [CallStatus::Pending, CallStatus::Initiated, CallStatus::Processing];

        $eligible = Campaign::query()
            ->processing()
            ->whereDoesntHave('calls', fn ($q) => $q->whereIn('status', $activeStates))
            ->withCount([
                'calls as total_count',
                'calls as completed_count' => fn ($q) => $q->where('status', CallStatus::Completed),
            ])
            ->get();

        if ($eligible->isEmpty()) {
            $this->info('No campaigns to complete.');

            return;
        }

        $finishedIds = $eligible
            ->filter(fn ($c) => $c->total_count > 0 && ($c->completed_count / $c->total_count) * 100 >= $callingSetting->campaign_success_threshold)
            ->pluck('id');

        $failedIds = $eligible->pluck('id')->diff($finishedIds);

        if ($finishedIds->isNotEmpty()) {
            Campaign::whereIn('id', $finishedIds)->update(['status' => CampaignStatus::Finished]);
        }

        if ($failedIds->isNotEmpty()) {
            Campaign::whereIn('id', $failedIds)->update(['status' => CampaignStatus::Failed]);
        }

        $this->info("Finished: {$finishedIds->count()}, Failed: {$failedIds->count()}");
    }
}
