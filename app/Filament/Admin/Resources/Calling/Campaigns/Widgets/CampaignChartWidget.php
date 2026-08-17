<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Campaigns\Widgets;

use App\Enums\CallStatus;
use App\Models\Campaign;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

final class CampaignChartWidget extends ChartWidget
{
    public ?Model $record = null;

    protected ?string $heading = 'Call Status Distribution';

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

        $counts = $this->record->calls()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statuses = [
            CallStatus::Pending,
            CallStatus::Initiated,
            CallStatus::Processing,
            CallStatus::Completed,
            CallStatus::Failed,
        ];

        $colors = [
            CallStatus::Pending->value => '#9CA3AF',
            CallStatus::Initiated->value => '#F59E0B',
            CallStatus::Processing->value => '#3B82F6',
            CallStatus::Completed->value => '#10B981',
            CallStatus::Failed->value => '#EF4444',
        ];

        return [
            'datasets' => collect($statuses)
                ->map(fn ($status) => [
                    'label' => $status->getLabel(),
                    'data' => [
                        (int) ($counts[$status->value] ?? 0),
                    ],
                    'backgroundColor' => $colors[$status->value],
                ])
                ->all(),

            'labels' => ['Calls'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
