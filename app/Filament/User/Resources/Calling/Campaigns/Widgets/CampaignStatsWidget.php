<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Campaigns\Widgets;

use App\Enums\CallStatus;
use App\Models\Campaign;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use LaraZeus\Tabler\Tabler;

final class CampaignStatsWidget extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '5s';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        if (! $this->record instanceof Campaign) {
            return [];
        }

        $stats = $this->record->calls()
            ->selectRaw('
                COUNT(*) as total_calls,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_calls,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed_calls,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_calls,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as processing_calls,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS initiated_calls,
                COALESCE(SUM(cost), 0) as total_cost
            ', [
                CallStatus::Completed->value,
                CallStatus::Failed->value,
                CallStatus::Pending->value,
                CallStatus::Processing->value,
                CallStatus::Initiated->value,
            ])
            ->first();

        $totalCalls = (int) $stats->total_calls;
        $completedCalls = (int) $stats->completed_calls;
        $failedCalls = (int) $stats->failed_calls;
        $pendingCalls = (int) $stats->pending_calls;
        $processingCalls = (int) $stats->processing_calls;
        $initiatedCalls = (int) $stats->initiated_calls;
        $totalCost = (float) $stats->total_cost;

        $completedRate = $totalCalls > 0
            ? round(($completedCalls / $totalCalls) * 100, 1)
            : 0;

        return [
            Stat::make('Total Calls', number_format($totalCalls))
                ->description('All calls in campaign'),

            Stat::make('Pending', number_format($pendingCalls))
                ->description('Waiting to be processed')
                ->icon(Tabler::PhonePlus),

            Stat::make('Initiated', number_format($initiatedCalls))
                ->description('Waiting for call to start')
                ->icon(Tabler::PhoneIncoming),

            Stat::make('Processing', number_format($processingCalls))
                ->description('Currently in progress')
                ->icon(Tabler::PhoneCall),

            Stat::make('Failed', number_format($failedCalls))
                ->description('Could not connect')
                ->icon(Tabler::PhoneX)
                ->color('danger'),

            Stat::make('Completed', number_format($completedCalls))
                ->description('Successfully completed')
                ->icon(Tabler::PhoneDone)
                ->color('success'),

            Stat::make('Completion', $completedRate.'%')
                ->description('Percentage of completed calls')
                ->color(
                    $completedRate >= 50
                        ? 'success'
                        : ($completedRate >= 25 ? 'warning' : 'danger')
                ),

            Stat::make('Total Cost', Number::currency($totalCost, 'BDT'))
                ->description('Campaign expenditure')
                ->color('primary'),
        ];
    }
}
