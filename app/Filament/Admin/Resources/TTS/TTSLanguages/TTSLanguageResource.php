<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSLanguages;

use App\Enums\AdminNavigationGroup;
use App\Filament\Admin\Resources\TTS\TTSLanguages\Pages\CreateTTSLanguage;
use App\Filament\Admin\Resources\TTS\TTSLanguages\Pages\EditTTSLanguage;
use App\Filament\Admin\Resources\TTS\TTSLanguages\Pages\ListTTSLanguages;
use App\Filament\Admin\Resources\TTS\TTSLanguages\Pages\ViewTTSLanguage;
use App\Filament\Admin\Resources\TTS\TTSLanguages\RelationManagers\ArtistsRelationManager;
use App\Filament\Admin\Resources\TTS\TTSLanguages\Schemas\TTSLanguageForm;
use App\Filament\Admin\Resources\TTS\TTSLanguages\Schemas\TTSLanguageInfolist;
use App\Filament\Admin\Resources\TTS\TTSLanguages\Tables\TTSLanguagesTable;
use App\Models\TTSLanguage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class TTSLanguageResource extends Resource
{
    protected static ?string $model = TTSLanguage::class;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::TextToSpeech;

    protected static ?string $breadcrumb = 'TTS Languages';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Languages';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'TTS Language';

    protected static ?string $pluralModelLabel = 'TTS Languages';

    protected static ?string $slug = 'text-to-speech/languages';

    public static function form(Schema $schema): Schema
    {
        return TTSLanguageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TTSLanguageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TTSLanguagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ArtistsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTTSLanguages::route('/'),
            'create' => CreateTTSLanguage::route('/create'),
            'view' => ViewTTSLanguage::route('/{record}'),
            'edit' => EditTTSLanguage::route('/{record}/edit'),
        ];
    }
}
