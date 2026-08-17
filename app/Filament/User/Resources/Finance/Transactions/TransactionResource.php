<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Finance\Transactions;

use App\Enums\UserNavigationGroup;
use App\Filament\User\Resources\Finance\Transactions\Pages\ListTransactions;
use App\Filament\User\Resources\Finance\Transactions\Pages\ViewTransaction;
use App\Filament\User\Resources\Finance\Transactions\Schemas\TransactionInfolist;
use App\Filament\User\Resources\Finance\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|UnitEnum|null $navigationGroup = UserNavigationGroup::Finance;

    protected static ?int $navigationSort = 60;

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
