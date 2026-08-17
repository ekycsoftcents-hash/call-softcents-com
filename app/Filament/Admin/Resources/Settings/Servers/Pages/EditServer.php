<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Settings\Servers\Pages;

use App\Filament\Admin\Resources\Settings\Servers\Concerns\TestsServerConnections;
use App\Filament\Admin\Resources\Settings\Servers\ServerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditServer extends EditRecord
{
    use TestsServerConnections;

    protected static string $resource = ServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->testServerConnections($this->data);
    }
}
