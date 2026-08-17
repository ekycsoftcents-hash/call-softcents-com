<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Audio\Pages;

use App\Filament\User\Resources\Calling\Audio\AudioResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewAudio extends ViewRecord
{
    protected static string $resource = AudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
