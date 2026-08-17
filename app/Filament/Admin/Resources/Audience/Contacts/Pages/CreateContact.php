<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Contacts\Pages;

use App\Filament\Admin\Resources\Audience\Contacts\ContactResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;
}
