<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Settings\Callers;

use App\Enums\AdminNavigationGroup;
use App\Filament\Admin\Resources\Settings\Callers\Pages\CreateCaller;
use App\Filament\Admin\Resources\Settings\Callers\Pages\EditCaller;
use App\Filament\Admin\Resources\Settings\Callers\Pages\ListCallers;
use App\Filament\Admin\Resources\Settings\Callers\Schemas\CallerForm;
use App\Filament\Admin\Resources\Settings\Callers\Tables\CallersTable;
use App\Models\Caller;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class CallerResource extends Resource
{
    protected static ?string $model = Caller::class;

    protected static string|null|UnitEnum $navigationGroup = AdminNavigationGroup::Settings;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'caller_name';

    public static function form(Schema $schema): Schema
    {
        return CallerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CallersTable::configure($table);
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
            'index' => ListCallers::route('/'),
            'create' => CreateCaller::route('/create'),
            'edit' => EditCaller::route('/{record}/edit'),
        ];
    }
}
