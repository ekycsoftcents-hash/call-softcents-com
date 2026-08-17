<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Groups\Pages;

use App\Filament\Admin\Resources\Audience\Groups\GroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListGroups extends ListRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
