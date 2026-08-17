<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Audio\Schemas;

use App\Enums\AudioType;
use App\Filament\Infolists\Components\AudioPlayerEntry;
use App\Models\Audio;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use LaraZeus\Tabler\Tabler;

final class AudioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Audio Details')
                            ->icon(Tabler::Music)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->icon(Tabler::H1)
                                    ->columnSpanFull(),
                                Grid::make(3)->schema([
                                    TextEntry::make('type')
                                        ->badge(),

                                    TextEntry::make('approval')
                                        ->badge()
                                        ->label('Approval'),
                                ]),

                                TextEntry::make('description')
                                    ->markdown()
                                    ->placeholder('No description provided.')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Attributes')
                            ->icon(Tabler::ListDetails)
                            ->visible(fn (Audio $record) => $record->type === AudioType::TTS)
                            ->collapsible()
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('ttsArtist.name')
                                        ->label('Artist')
                                        ->badge()
                                        ->placeholder('-')
                                        ->icon(Tabler::Microphone),
                                    TextEntry::make('ttsArtist.ttsLanguage.name')
                                        ->label('Language')
                                        ->badge()
                                        ->placeholder('-')
                                        ->icon(Tabler::Language),
                                    TextEntry::make('ttsArtist.gender')
                                        ->badge()
                                        ->placeholder('-'),
                                ]),
                                TextEntry::make('message')
                                    ->label('Message Content')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('File Specifications')
                            ->icon(Tabler::FileSignal)
                            ->schema([
                                TextEntry::make('duration')
                                    ->label('Duration')
                                    ->numeric()
                                    ->formatStateUsing(fn (int $state) => secondsToHuman($state))
                                    ->icon(Tabler::Clock),
                                TextEntry::make('size')
                                    ->label('Size')
                                    ->numeric()
                                    ->formatStateUsing(fn (int $state) => bytesToHuman($state))
                                    ->icon(Tabler::Database),
                            ]),

                        Section::make('Preview')
                            ->icon(Tabler::PlayerPlay)
                            ->schema([
                                AudioPlayerEntry::make('original_path')
                                    ->label('Original Audio')
                                    ->hiddenLabel(),
                                AudioPlayerEntry::make('converted_path')
                                    ->label('Converted Audio')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }
}
