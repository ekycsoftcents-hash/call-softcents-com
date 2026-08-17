<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Groups\RelationManagers;

use App\Filament\User\Resources\Audience\Contacts\ContactResource;
use Filament\Resources\RelationManagers\RelationManager;

final class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    protected static ?string $relatedResource = ContactResource::class;
}
