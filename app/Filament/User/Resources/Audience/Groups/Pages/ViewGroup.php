<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Groups\Pages;

use App\Filament\Imports\ContactImporter;
use App\Filament\User\Resources\Audience\Groups\GroupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

final class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make('import')
                ->icon(Heroicon::OutlinedDocumentArrowUp)
                ->importer(ContactImporter::class)
                ->options(['group_id' => $this->record->id]),
            EditAction::make(),
        ];
    }
}
