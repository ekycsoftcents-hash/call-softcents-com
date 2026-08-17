<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Campaign;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

final class CampaignStatusChart extends ChartWidget
{
    protected ?string $heading = 'Campaign Status Distribution';

    protected static ?int $sort = 7;

    protected function getData(): array
    {
        $data = Campaign::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Enum/Label mapping
        $labels = collect($data)
            ->keys()
            ->map(fn ($key) => Str::headline($key));
        $values = array_values($data);

        return [
            'datasets' => [
                [
                    'label' => 'Campaigns',
                    'data' => $values,
                    'backgroundColor' => [
                        '#16a34a', // green-600 (Completed)
                        '#dc2626', // red-600 (Failed)
                        '#ca8a04', // yellow-600 (Processing)
                        '#2563eb', // blue-600
                        '#9ca3af', // gray-400 (Pending/Cancelled)
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
