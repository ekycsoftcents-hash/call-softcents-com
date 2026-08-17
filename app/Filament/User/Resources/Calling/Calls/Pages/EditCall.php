<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Calls\Pages;

use App\Filament\User\Resources\Calling\Calls\CallResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditCall extends EditRecord
{
    protected static string $resource = CallResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
