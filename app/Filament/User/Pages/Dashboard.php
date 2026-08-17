<?php

declare(strict_types=1);

namespace App\Filament\User\Pages;

use App\Filament\User\Resources\Finance\Deposits\Actions\DepositAction;
use Filament\Pages\Dashboard as BaseDashboard;

final class Dashboard extends BaseDashboard
{
    protected function getActions(): array
    {
        return [
            DepositAction::make(),
        ];
    }
}
