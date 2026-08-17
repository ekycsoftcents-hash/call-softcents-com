<?php

declare(strict_types=1);

namespace App\Filament\User\Widgets;

use App\Models\Call;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

final class CallsPerDayChart extends ChartWidget
{
    public ?string $filter = 'week';

    protected ?string $heading = 'Calls Per Day';

    protected static ?int $sort = 2;

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'This Year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $query = Call::query()->where('user_id', auth()->id());

        $trend = match ($activeFilter) {
            'today' => Trend::model(Call::class)
                ->query($query)
                ->between(
                    start: now()->startOfDay(),
                    end: now()->endOfDay(),
                )
                ->perHour()
                ->count(),
            'week' => Trend::model(Call::class)
                ->query($query)
                ->between(
                    start: now()->subWeek(),
                    end: now(),
                )
                ->perDay()
                ->count(),
            'month' => Trend::model(Call::class)
                ->query($query)
                ->between(
                    start: now()->subMonth(),
                    end: now(),
                )
                ->perDay()
                ->count(),
            'year' => Trend::model(Call::class)
                ->query($query)
                ->between(
                    start: now()->startOfYear(),
                    end: now(),
                )
                ->perMonth()
                ->count(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Calls',
                    'data' => $trend->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $trend->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
