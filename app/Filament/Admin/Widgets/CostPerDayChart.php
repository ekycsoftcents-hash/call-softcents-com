<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Call;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

final class CostPerDayChart extends ChartWidget
{
    public ?string $filter = 'week';

    protected ?string $heading = 'Revenue (Cost)';

    protected static ?int $sort = 3;

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

        $trend = match ($activeFilter) {
            'today' => Trend::model(Call::class)
                ->between(
                    start: now()->startOfDay(),
                    end: now()->endOfDay(),
                )
                ->perHour()
                ->sum('cost'),
            'week' => Trend::model(Call::class)
                ->between(
                    start: now()->subWeek(),
                    end: now(),
                )
                ->perDay()
                ->sum('cost'),
            'month' => Trend::model(Call::class)
                ->between(
                    start: now()->subMonth(),
                    end: now(),
                )
                ->perDay()
                ->sum('cost'),
            'year' => Trend::model(Call::class)
                ->between(
                    start: now()->startOfYear(),
                    end: now(),
                )
                ->perMonth()
                ->sum('cost'),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (BDT)',
                    'data' => $trend->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#EA580C', // Orange-600
                    'borderColor' => '#EA580C',
                ],
            ],
            'labels' => $trend->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
