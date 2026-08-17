<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Audio\Pages;

use App\Filament\User\Resources\Calling\Audio\AudioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListAudio extends ListRecords
{
    protected static string $resource = AudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
