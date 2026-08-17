<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Groups\Pages;

use App\Filament\Admin\Resources\Audience\Contacts\ContactResource;
use App\Filament\Admin\Resources\Audience\Groups\GroupResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('create_contact')
                ->label('Add Contact')
                ->url(fn () => ContactResource::getUrl('create')),
        ];
    }
}
