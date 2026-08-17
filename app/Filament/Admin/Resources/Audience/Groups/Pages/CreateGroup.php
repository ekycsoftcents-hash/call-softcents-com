<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Groups\Pages;

use App\Filament\Admin\Resources\Audience\Groups\GroupResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;
}
