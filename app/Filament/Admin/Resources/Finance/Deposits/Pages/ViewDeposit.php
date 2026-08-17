<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Deposits\Pages;

use App\Filament\Admin\Resources\Finance\Deposits\DepositResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewDeposit extends ViewRecord
{
    protected static string $resource = DepositResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
