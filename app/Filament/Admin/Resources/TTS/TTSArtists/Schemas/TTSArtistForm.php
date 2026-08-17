<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSArtists\Schemas;

use App\Enums\TTSArtistGender;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class TTSArtistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        Select::make('tts_language_id')
                            ->relationship('ttsLanguage', 'name')
                            ->label('Language')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Artist Name')
                            ->required(),
                        TextInput::make('code')
                            ->label('Artist Code')
                            ->required(),
                        Select::make('gender')
                            ->label('Gender')
                            ->options(TTSArtistGender::class)
                            ->required(),
                        Toggle::make('enabled')
                            ->label('Enabled')
                            ->required(),
                    ]),
            ]);
    }
}
