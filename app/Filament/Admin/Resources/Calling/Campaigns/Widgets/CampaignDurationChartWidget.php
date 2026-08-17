<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Campaigns\Widgets;

use App\Enums\CallStatus;
use App\Models\Campaign;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

final class CampaignDurationChartWidget extends ChartWidget
{
    public ?Model $record = null;

    protected ?string $heading = 'Completed Call Duration';

    protected ?string $maxHeight = '200px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        if (! $this->record instanceof Campaign) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $calls = $this->record->calls()
            ->where('status', CallStatus::Completed)
            ->pluck('duration');

        $buckets = [
            '0–10s' => 0,
            '10–30s' => 0,
            '30–60s' => 0,
            '1–2m' => 0,
            '2m+' => 0,
        ];

        foreach ($calls as $duration) {
            if ($duration <= 10) {
                $buckets['0–10s']++;
            } elseif ($duration <= 30) {
                $buckets['10–30s']++;
            } elseif ($duration <= 60) {
                $buckets['30–60s']++;
            } elseif ($duration <= 120) {
                $buckets['1–2m']++;
            } else {
                $buckets['2m+']++;
            }
        }

        return [
            'datasets' => [[
                'label' => 'Completed Calls',
                'data' => array_values($buckets),
                'backgroundColor' => [
                    '#60A5FA',
                    '#34D399',
                    '#FBBF24',
                    '#F97316',
                    '#EF4444',
                ],
            ]],

            'labels' => array_keys($buckets),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
