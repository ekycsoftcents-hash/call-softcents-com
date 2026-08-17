<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Campaigns\RelationManagers;

use App\Filament\Admin\Resources\Calling\Calls\CallResource;
use Filament\Resources\RelationManagers\RelationManager;

final class CallsRelationManager extends RelationManager
{
    protected static string $relationship = 'calls';

    protected static ?string $relatedResource = CallResource::class;
}
