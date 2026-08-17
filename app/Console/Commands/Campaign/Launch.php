<?php

declare(strict_types=1);

namespace App\Console\Commands\Campaign;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

#[Signature('campaign:launch')]
#[Description('Launch a campaign, changing its status to processing and allowing calls to be dispatched')]
final class Launch extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Campaign::approved()
            ->pending()
            ->where(function (Builder $query) {
                $query->whereNull('scheduled_at')
                    ->orWherePast('scheduled_at');
            })
            ->chunk(100, function ($campaigns) {
                foreach ($campaigns as $campaign) {
                    $campaign->update(['status' => CampaignStatus::Processing]);
                    $this->info("Launched campaign ID {$campaign->id}");
                }
            });
    }
}
