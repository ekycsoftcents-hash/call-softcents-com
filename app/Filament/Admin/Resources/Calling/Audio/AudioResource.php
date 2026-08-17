<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Audio;

use App\Enums\AdminNavigationGroup;
use App\Enums\AudioApproval;
use App\Filament\Admin\Resources\Calling\Audio\Pages\ListAudio;
use App\Filament\Admin\Resources\Calling\Audio\Pages\ViewAudio;
use App\Filament\Admin\Resources\Calling\Audio\Schemas\AudioInfolist;
use App\Filament\Admin\Resources\Calling\Audio\Tables\AudioTable;
use App\Models\Audio;
use App\Models\Scopes\OwnedByAuthUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use LaraZeus\Tabler\Tabler;
use UnitEnum;

final class AudioResource extends Resource
{
    protected static ?string $model = Audio::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Music;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Calling;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        $count = Audio::whereApproval(AudioApproval::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function infolist(Schema $schema): Schema
    {
        return AudioInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AudioTable::configure($table);
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
            'index' => ListAudio::route('/'),
            'view' => ViewAudio::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                OwnedByAuthUser::class,
            ]);
    }
}
