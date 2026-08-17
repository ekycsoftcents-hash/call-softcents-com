<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Contacts\Pages;

use App\Filament\User\Resources\Audience\Contacts\ContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
