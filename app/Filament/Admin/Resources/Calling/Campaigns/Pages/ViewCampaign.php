<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Campaigns\Pages;

use App\Filament\Admin\Resources\Calling\Campaigns\CampaignResource;
use App\Filament\Admin\Resources\Calling\Campaigns\Widgets\CampaignChartWidget;
use App\Filament\Admin\Resources\Calling\Campaigns\Widgets\CampaignDurationChartWidget;
use App\Filament\Admin\Resources\Calling\Campaigns\Widgets\CampaignStatsWidget;
use Filament\Resources\Pages\ViewRecord;

final class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            CampaignStatsWidget::class,
            CampaignChartWidget::class,
            CampaignDurationChartWidget::class
        ];
    }
}
