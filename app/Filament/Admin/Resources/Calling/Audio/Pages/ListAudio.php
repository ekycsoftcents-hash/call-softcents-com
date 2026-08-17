<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Audio\Pages;

use App\Filament\Admin\Resources\Calling\Audio\AudioResource;
use Filament\Resources\Pages\ListRecords;

final class ListAudio extends ListRecords
{
    protected static string $resource = AudioResource::class;
}
