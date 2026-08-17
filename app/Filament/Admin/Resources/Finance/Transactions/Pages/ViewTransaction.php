<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Transactions\Pages;

use App\Filament\Admin\Resources\Finance\Transactions\TransactionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
