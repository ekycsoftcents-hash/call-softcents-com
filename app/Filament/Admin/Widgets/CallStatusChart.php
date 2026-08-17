<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Call;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

final class CallStatusChart extends ChartWidget
{
    protected ?string $heading = 'Call Status Distribution';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = Call::query()
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
                    'label' => 'Calls',
                    'data' => $values,
                    'backgroundColor' => [
                        '#16a34a', // green-600 (Completed)
                        '#dc2626', // red-600 (Failed)
                        '#ca8a04', // yellow-600
                        '#2563eb', // blue-600
                        '#9333ea', // purple-600
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
