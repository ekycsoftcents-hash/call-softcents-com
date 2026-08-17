<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Audio\Schemas;

use App\Enums\AudioType;
use App\Enums\TTSArtistGender;
use App\Models\Audio;
use App\Models\TTSArtist;
use App\Models\TTSLanguage;
use App\Settings\TTSSetting;
use Auth;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class AudioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->heading('Basic Information')
                    ->description('Provide basic information about the audio')
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->placeholder('Enter audio title')
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Enter audio description')
                            ->columnSpanFull(),
                        Textarea::make('message')
                            ->label('Script / Message')
                            ->placeholder('Enter the script or message for TTS audio')
                            ->rows(5)
                            ->columnSpanFull()
                            ->disabledOn(['edit', 'view'])
                            ->visible(fn (Get $get) => $get('type') === AudioType::TTS->value)
                            ->required(fn (Get $get) => $get('type') === AudioType::TTS->value),
                    ]),
                Section::make()
                    ->heading('Audio Source')
                    ->description('Select Audio Source')
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options(AudioType::availableOptions(Auth::user()->audio_type))
                            ->searchable()
                            ->default(AudioType::Upload)
                            ->selectablePlaceholder(false)
                            ->prefixIcon(Tabler::Category)
                            ->disabledOn(['edit', 'view'])
                            ->live()
                            ->required(),
                        FileUpload::make('original_path')
                            ->label('Upload Audio File')
                            ->directory('audios/originals')
                            ->disk(config('filesystems.default'))
                            ->acceptedFileTypes(['audio/*'])
                            ->maxSize(10240) // 10 MB
                            ->disabledOn(['edit', 'view'])
                            ->visible(fn (Get $get) => $get('type') === AudioType::Upload->value)
                            ->required(fn (Get $get) => $get('type') === AudioType::Upload->value),

                        Group::make()
                            ->visible(fn (Get $get) => $get('type') === AudioType::TTS->value)
                            ->schema([
                                Select::make('language_id')
                                    ->label('Language')
                                    ->options(function () {
                                        return TTSLanguage::scopes(['enabled'])
                                            ->whereEngine(app(TTSSetting::class)->engine)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->disabledOn(['edit', 'view'])
                                    ->preload()
                                    ->live()
                                    ->dehydrated(false)
                                    ->prefixIcon(Tabler::Language)
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('gender', null);
                                        $set('tts_artist_id', null);
                                    })
                                    ->afterStateHydrated(function (Select $component, ?Audio $record) {
                                        if (! $record) {
                                            return;
                                        }

                                        if ($record->tts_artist_id && $artist = TTSArtist::find($record->tts_artist_id)) {
                                            $component->state($artist->tts_language_id);
                                        }
                                    })
                                    ->required(fn (Get $get) => $get('type') === AudioType::TTS->value),
                                Select::make('gender')
                                    ->label('Gender')
                                    ->options(TTSArtistGender::class)
                                    ->live()
                                    ->dehydrated(false)
                                    ->prefixIcon(Tabler::Man)
                                    ->disabledOn(['edit', 'view'])
                                    ->afterStateUpdated(fn (Set $set) => $set('tts_artist_id', null))
                                    ->afterStateHydrated(function (Select $component, ?Audio $record) {
                                        if (! $record) {
                                            return;
                                        }

                                        if ($record->tts_artist_id && $artist = TTSArtist::find($record->tts_artist_id)) {
                                            $component->state($artist->gender);
                                        }
                                    })
                                    ->required(fn (Get $get) => $get('type') === AudioType::TTS->value),
                                Select::make('tts_artist_id')
                                    ->label('Artist')
                                    ->options(fn (Get $get) => TTSArtist::scopes(['enabled'])
                                        ->where('tts_language_id', $get('language_id'))
                                        ->where('gender', $get('gender'))
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->prefixIcon(Tabler::Microphone)
                                    ->disabledOn(['edit', 'view'])
                                    ->visible(fn (Get $get) => $get('language_id') && $get('gender'))
                                    ->required(fn (Get $get) => $get('type') === AudioType::TTS->value),
                            ]),

                    ]),
            ]);
    }
}
