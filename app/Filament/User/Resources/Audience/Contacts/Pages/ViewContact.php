<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Contacts\Pages;

use App\Filament\User\Resources\Audience\Contacts\ContactResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
