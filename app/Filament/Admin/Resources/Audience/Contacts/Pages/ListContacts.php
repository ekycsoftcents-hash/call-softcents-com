<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Contacts\Pages;

use App\Filament\Admin\Resources\Audience\Contacts\ContactResource;
use Filament\Resources\Pages\ListRecords;

final class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;
}
