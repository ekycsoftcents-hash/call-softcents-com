<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Contacts\Pages;

use App\Filament\User\Resources\Audience\Contacts\ContactResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;
}
