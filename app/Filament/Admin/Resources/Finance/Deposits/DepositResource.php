<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Deposits;

use App\Enums\AdminNavigationGroup;
use App\Filament\Admin\Resources\Finance\Deposits\Pages\CreateDeposit;
use App\Filament\Admin\Resources\Finance\Deposits\Pages\ListDeposits;
use App\Filament\Admin\Resources\Finance\Deposits\Pages\ViewDeposit;
use App\Filament\Admin\Resources\Finance\Deposits\Schemas\DepositForm;
use App\Filament\Admin\Resources\Finance\Deposits\Schemas\DepositInfolist;
use App\Filament\Admin\Resources\Finance\Deposits\Tables\DepositsTable;
use App\Models\Deposit;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Finance;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'transaction_id';

    public static function form(Schema $schema): Schema
    {
        return DepositForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepositsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DepositInfolist::configure($schema);
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
            'index' => ListDeposits::route('/'),
            'view' => ViewDeposit::route('/{record}'),
            'create' => CreateDeposit::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
