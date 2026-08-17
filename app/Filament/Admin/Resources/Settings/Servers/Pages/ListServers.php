<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Settings\Servers\Pages;

use App\Filament\Admin\Resources\Settings\Servers\ServerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListServers extends ListRecords
{
    protected static string $resource = ServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
