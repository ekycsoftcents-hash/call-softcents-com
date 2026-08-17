<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers;

use App\Enums\UserType;
use App\Filament\Admin\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Admin\Resources\Customers\Pages\EditCustomer;
use App\Filament\Admin\Resources\Customers\Pages\ListCustomers;
use App\Filament\Admin\Resources\Customers\Pages\ViewCustomer;
use App\Filament\Admin\Resources\Customers\Schemas\CustomerForm;
use App\Filament\Admin\Resources\Customers\Tables\CustomersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Customers';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Customers';

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return Schemas\CustomerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
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
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'view' => ViewCustomer::route('/{record}'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', UserType::User);
    }
}
