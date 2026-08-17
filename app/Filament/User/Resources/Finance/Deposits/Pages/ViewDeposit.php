<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Finance\Deposits\Pages;

use App\Filament\User\Resources\Finance\Deposits\DepositResource;
use Filament\Resources\Pages\ViewRecord;

final class ViewDeposit extends ViewRecord
{
    protected static string $resource = DepositResource::class;
}
