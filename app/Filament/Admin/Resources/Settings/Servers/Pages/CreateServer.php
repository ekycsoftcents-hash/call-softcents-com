<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Settings\Servers\Pages;

use App\Filament\Admin\Resources\Settings\Servers\Concerns\TestsServerConnections;
use App\Filament\Admin\Resources\Settings\Servers\ServerResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateServer extends CreateRecord
{
    use TestsServerConnections;

    protected static string $resource = ServerResource::class;

    protected function beforeCreate(): void
    {
        $this->testServerConnections($this->data);
    }
}
