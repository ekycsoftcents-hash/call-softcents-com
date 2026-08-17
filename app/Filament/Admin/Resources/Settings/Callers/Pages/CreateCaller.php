<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Settings\Callers\Pages;

use App\Filament\Admin\Resources\Settings\Callers\CallerResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateCaller extends CreateRecord
{
    protected static string $resource = CallerResource::class;
}
