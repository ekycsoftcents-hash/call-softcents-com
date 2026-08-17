<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Calls;

use App\Enums\UserNavigationGroup;
use App\Filament\User\Resources\Calling\Calls\Pages\CreateCall;
use App\Filament\User\Resources\Calling\Calls\Pages\ListCalls;
use App\Filament\User\Resources\Calling\Calls\Pages\ViewCall;
use App\Filament\User\Resources\Calling\Calls\RelationManagers\TransactionsRelationManager;
use App\Filament\User\Resources\Calling\Calls\Schemas\CallForm;
use App\Filament\User\Resources\Calling\Calls\Schemas\CallInfolist;
use App\Filament\User\Resources\Calling\Calls\Tables\CallsTable;
use App\Models\Call;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use LaraZeus\Tabler\Tabler;
use UnitEnum;

final class CallResource extends Resource
{
    protected static ?string $model = Call::class;

    protected static ?string $recordTitleAttribute = 'phone_number';

    protected static string|BackedEnum|null $navigationIcon = Tabler::PhoneCall;

    protected static string|UnitEnum|null $navigationGroup = UserNavigationGroup::Calling;

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return CallForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CallsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CallInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalls::route('/'),
            'create' => CreateCall::route('/create'),
            'view' => ViewCall::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
