<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Campaigns\RelationManagers;

use App\Filament\Exports\User\CallExporter;
use App\Filament\User\Resources\Calling\Calls\CallResource;
use Filament\Actions\ExportAction;
use Filament\Resources\RelationManagers\RelationManager;

final class CallsRelationManager extends RelationManager
{
    protected static string $relationship = 'calls';

    protected static ?string $relatedResource = CallResource::class;

    protected function getTableHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(CallExporter::class),
        ];
    }
}
