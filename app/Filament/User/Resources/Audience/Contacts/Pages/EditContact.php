<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Contacts\Pages;

use App\Filament\User\Resources\Audience\Contacts\ContactResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

final class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
