<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Transactions\Pages;

use App\Filament\Admin\Resources\Finance\Transactions\TransactionResource;
use Filament\Resources\Pages\ListRecords;

final class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;
}
