<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Audio\Schemas;

use App\Enums\AudioType;
use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Filament\Infolists\Components\AudioPlayerEntry;
use App\Models\Audio;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class AudioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Audio Overview Section
                Section::make('Audio Overview')
                    ->description('Essential audio file information')
                    ->icon(Tabler::Music)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title')
                            ->icon(Tabler::FileMusic)
                            ->columnSpanFull(),
                        TextEntry::make('type')
                            ->label('Type')
                            ->icon(Tabler::Tag)
                            ->badge(),
                        TextEntry::make('approval')
                            ->label('Approval Status')
                            ->icon(Tabler::CircleCheck)
                            ->badge()
                            ->color(fn ($state) => match ($state?->value ?? $state) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('user.name')
                            ->label('Uploaded By')
                            ->icon(Tabler::User)
                            ->badge()
                            ->url(fn (Audio $record) => CustomerResource::getUrl('view', ['record' => $record->user_id])),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // Description Section
                Section::make('Description')
                    ->description('Audio file description')
                    ->icon(Tabler::FileText)
                    ->schema([
                        TextEntry::make('description')
                            ->markdown()
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // File Specifications Section
                Section::make('File Specifications')
                    ->description('Audio file technical details')
                    ->icon(Tabler::FileSignal)
                    ->schema([
                        TextEntry::make('duration')
                            ->label('Duration')
                            ->icon(Tabler::Clock)
                            ->numeric()
                            ->formatStateUsing(fn (float $state) => secondsToHuman($state)),
                        TextEntry::make('size')
                            ->label('File Size')
                            ->icon(Tabler::Database)
                            ->numeric()
                            ->formatStateUsing(fn (int $state) => bytesToHuman($state)),
                    ])
                    ->columns()
                    ->collapsible(),

                // TTS Attributes Section
                Section::make('TTS Attributes')
                    ->description('Text-to-speech configuration')
                    ->icon(Tabler::Microphone)
                    ->visible(fn (Audio $record) => $record->type === AudioType::TTS)
                    ->schema([
                        TextEntry::make('ttsArtist.name')
                            ->label('Voice Artist')
                            ->icon(Tabler::User)
                            ->badge()
                            ->placeholder('Not specified'),
                        TextEntry::make('ttsArtist.ttsLanguage.name')
                            ->label('Language')
                            ->icon(Tabler::Language)
                            ->badge()
                            ->placeholder('Not specified'),
                        TextEntry::make('ttsArtist.gender')
                            ->label('Gender')
                            ->icon(Tabler::Users)
                            ->badge()
                            ->placeholder('Not specified'),
                        TextEntry::make('message')
                            ->label('Message Content')
                            ->placeholder('No message content')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // Audio Preview Section
                Section::make('Audio Preview')
                    ->description('Listen to audio files')
                    ->icon(Tabler::PlayerPlay)
                    ->schema([
                        AudioPlayerEntry::make('original_path')
                            ->label('Original Audio')
                            ->hiddenLabel(),
                        AudioPlayerEntry::make('converted_path')
                            ->label('Converted Audio')
                            ->hiddenLabel()
                            ->visible(fn (Audio $record) => ! empty($record->converted_path)),
                    ])
                    ->collapsible(),

                // System Information Section
                Section::make('System Information')
                    ->description('Record timestamps')
                    ->icon(Tabler::InfoCircle)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->icon(Tabler::CalendarPlus)
                            ->since()
                            ->tooltip(fn (Audio $record) => $record->created_at ? $record->created_at->format('M j, Y \a\t h:i A') : 'Unknown')
                            ->color('gray'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->icon(Tabler::Refresh)
                            ->since()
                            ->tooltip(fn (Audio $record) => $record->updated_at ? $record->updated_at->format('M j, Y \a\t h:i A') : 'Never updated')
                            ->color('gray'),
                    ])
                    ->columns()
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
