<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Calls\Pages;

use App\Filament\Admin\Resources\Calling\Calls\CallResource;
use Filament\Resources\Pages\ListRecords;

final class ListCalls extends ListRecords
{
    protected static string $resource = CallResource::class;
}
