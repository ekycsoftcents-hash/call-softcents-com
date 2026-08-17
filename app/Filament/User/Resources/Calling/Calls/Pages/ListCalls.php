<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Calls\Pages;

use App\Filament\User\Resources\Calling\Calls\CallResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListCalls extends ListRecords
{
    protected static string $resource = CallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
