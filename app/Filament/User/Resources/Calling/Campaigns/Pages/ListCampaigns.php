<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Campaigns\Pages;

use App\Filament\User\Resources\Calling\Campaigns\CampaignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
