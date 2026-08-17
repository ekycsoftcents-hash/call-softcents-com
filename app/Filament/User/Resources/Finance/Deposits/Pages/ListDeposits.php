<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Finance\Deposits\Pages;

use App\Filament\User\Resources\Finance\Deposits\Actions\DepositAction;
use App\Filament\User\Resources\Finance\Deposits\DepositResource;
use Filament\Resources\Pages\ListRecords;

final class ListDeposits extends ListRecords
{
    protected static string $resource = DepositResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DepositAction::make(),
        ];
    }
}
