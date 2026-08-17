<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Transactions;

use App\Enums\AdminNavigationGroup;
use App\Filament\Admin\Resources\Finance\Transactions\Pages\ListTransactions;
use App\Filament\Admin\Resources\Finance\Transactions\Pages\ViewTransaction;
use App\Filament\Admin\Resources\Finance\Transactions\Schemas\TransactionInfolist;
use App\Filament\Admin\Resources\Finance\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Finance;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function infolist(Schema $schema): Schema
    {
        return TransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'view' => ViewTransaction::route('/{record}'),
        ];
    }
}
