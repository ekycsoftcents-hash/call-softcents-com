<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Campaigns\Pages;

use App\Actions\CreateNewCampaign;
use App\Filament\User\Resources\Calling\Campaigns\CampaignResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Throwable;

final class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    /**
     * @throws Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateNewCampaign::class)->handle(auth()->user(), $data);
    }
}
