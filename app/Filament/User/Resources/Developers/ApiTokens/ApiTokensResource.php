<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Developers\ApiTokens;

use App\Enums\UserNavigationGroup;
use App\Filament\User\Resources\Developers\ApiTokens\Pages\ListApiTokens;
use App\Filament\User\Resources\Developers\ApiTokens\Tables\ApiTokensTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\PersonalAccessToken;
use UnitEnum;

final class ApiTokensResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static string|UnitEnum|null $navigationGroup = UserNavigationGroup::Developers;

    protected static ?string $modelLabel = 'API Token';

    protected static ?string $navigationLabel = 'API Tokens';

    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return ApiTokensTable::configure($table);
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
            'index' => ListApiTokens::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', auth()->id());
    }
}
