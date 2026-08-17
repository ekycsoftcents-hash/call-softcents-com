<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Audio;

use App\Enums\UserNavigationGroup;
use App\Filament\User\Resources\Calling\Audio\Pages\CreateAudio;
use App\Filament\User\Resources\Calling\Audio\Pages\EditAudio;
use App\Filament\User\Resources\Calling\Audio\Pages\ListAudio;
use App\Filament\User\Resources\Calling\Audio\Pages\ViewAudio;
use App\Filament\User\Resources\Calling\Audio\Schemas\AudioForm;
use App\Filament\User\Resources\Calling\Audio\Schemas\AudioInfolist;
use App\Filament\User\Resources\Calling\Audio\Tables\AudioTable;
use App\Models\Audio;
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

    protected static string|UnitEnum|null $navigationGroup = UserNavigationGroup::Calling;

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AudioForm::configure($schema);
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
            'create' => CreateAudio::route('/create'),
            'view' => ViewAudio::route('/{record}'),
            'edit' => EditAudio::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
