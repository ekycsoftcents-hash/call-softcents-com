<?php

declare(strict_types=1);

namespace App\Filament\User\Widgets;

use App\Enums\CallStatus;
use Carbon\CarbonInterval;
use Exception;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use LaraZeus\Tabler\Tabler;

final class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    /**
     * @throws Exception
     */
    protected function getStats(): array
    {
        $user = auth()->user();

        $callStats = $user->calls()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = ? THEN duration ELSE 0 END) as total_duration', [CallStatus::Completed->value])
            ->first();

        $balance = $user->balance;
        $pulseRate = $user->pulse_rate;
        $pulseDuration = $user->pulse_duration;
        $remainingCalls = $pulseRate > 0 ? (int) floor($balance / $pulseRate) : 0;

        $totalDuration = CarbonInterval::seconds($callStats->total_duration ?? 0)
            ->cascade()
            ->forHumans(short: true);

        return [
            Stat::make('Account Balance', Number::currency($balance, 'BDT'))
                ->description('Current balance in your account')
                ->icon(Tabler::PigMoney),

            Stat::make('Total Calls Made', Number::abbreviate($callStats->total ?? 0, maxPrecision: 2))
                ->description('Number of calls you have made')
                ->icon(Tabler::PhoneCalling),

            Stat::make('Total Call Duration', $totalDuration)
                ->description('Total duration of completed calls')
                ->icon(Tabler::Clock),

            Stat::make('Remaining Calls', Number::abbreviate($remainingCalls, maxPrecision: 2))
                ->description('Estimated number of calls')
                ->icon(Tabler::PhoneCalling),

            Stat::make('Pulse Rate', Number::currency($pulseRate, 'BDT', precision: 6))
                ->description('Cost per pulse')
                ->icon(Tabler::HeartRateMonitor),

            Stat::make('Pulse Duration', $pulseDuration.' seconds')
                ->description('Duration of one pulse')
                ->icon(Tabler::Clock),
        ];
    }
}
