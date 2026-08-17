<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSArtists;

use App\Enums\AdminNavigationGroup;
use App\Filament\Admin\Resources\TTS\TTSArtists\Pages\CreateTTSArtist;
use App\Filament\Admin\Resources\TTS\TTSArtists\Pages\EditTTSArtist;
use App\Filament\Admin\Resources\TTS\TTSArtists\Pages\ListTTSArtists;
use App\Filament\Admin\Resources\TTS\TTSArtists\Schemas\TTSArtistForm;
use App\Filament\Admin\Resources\TTS\TTSArtists\Tables\TTSArtistsTable;
use App\Models\TTSArtist;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class TTSArtistResource extends Resource
{
    protected static ?string $model = TTSArtist::class;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::TextToSpeech;

    protected static ?string $breadcrumb = 'TTS Artists';

    protected static ?int $navigationSort = 31;

    protected static ?string $navigationLabel = 'Artists';

    protected static ?string $modelLabel = 'TTS Artist';

    protected static ?string $pluralModelLabel = 'TTS Artists';

    protected static ?string $slug = 'text-to-speech/artists';

    public static function form(Schema $schema): Schema
    {
        return TTSArtistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TTSArtistsTable::configure($table);
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
            'index' => ListTTSArtists::route('/'),
            'create' => CreateTTSArtist::route('/create'),
            'edit' => EditTTSArtist::route('/{record}/edit'),
        ];
    }
}
