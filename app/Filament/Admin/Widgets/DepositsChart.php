<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\DepositStatus;
use App\Models\Deposit;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

final class DepositsChart extends ChartWidget
{
    public ?string $filter = 'week';

    protected ?string $heading = 'Deposits';

    protected static ?int $sort = 5;

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

        $query = Deposit::query()->where('status', DepositStatus::Completed);

        $trend = match ($activeFilter) {
            'today' => Trend::model(Deposit::class)
                ->query($query)
                ->between(
                    start: now()->startOfDay(),
                    end: now()->endOfDay(),
                )
                ->perHour()
                ->sum('amount'),
            'week' => Trend::model(Deposit::class)
                ->query($query)
                ->between(
                    start: now()->subWeek(),
                    end: now(),
                )
                ->perDay()
                ->sum('amount'),
            'month' => Trend::model(Deposit::class)
                ->query($query)
                ->between(
                    start: now()->subMonth(),
                    end: now(),
                )
                ->perDay()
                ->sum('amount'),
            'year' => Trend::model(Deposit::class)
                ->query($query)
                ->between(
                    start: now()->startOfYear(),
                    end: now(),
                )
                ->perMonth()
                ->sum('amount'),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Deposits (BDT)',
                    'data' => $trend->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#0ea5e9', // Sky-500
                    'borderColor' => '#0ea5e9',
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
