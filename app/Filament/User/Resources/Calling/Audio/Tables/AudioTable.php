<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Audio\Tables;

use App\Enums\AudioConversionStatus;
use App\Enums\AudioTTSStatus;
use App\Enums\AudioType;
use App\Models\Audio;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use LaraZeus\Tabler\Tabler;

final class AudioTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width(0)
                    ->alignCenter(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($state): string => (string) $state),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('approval')
                    ->label('Approval')
                    ->badge(),
                TextColumn::make('conversion_status')
                    ->label('Conversion')
                    ->badge(),
                TextColumn::make('tts_status')
                    ->label('TTS')
                    ->badge(),
                TextColumn::make('duration')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => secondsToHuman($state))
                    ->placeholder(secondsToHuman(0)),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ])
            ->recordActions([
                ActionGroup::make([
                    MediaAction::make('converted_url')
                        ->label('Play Converted')
                        ->icon(Tabler::Music)
                        ->media(fn (Audio $record) => getFileUrl($record->converted_path))
                        ->mediaType(MediaAction::TYPE_AUDIO)
                        ->autoplay()
                        ->visible(fn (Audio $record) => $record->conversion_status === AudioConversionStatus::Completed),
                    MediaAction::make('original_url')
                        ->label('Play Original')
                        ->icon(Tabler::Music)
                        ->media(fn (Audio $record) => getFileUrl($record->original_path))
                        ->mediaType(MediaAction::TYPE_AUDIO)
                        ->autoplay()
                        ->visible(fn (Audio $record) => $record->type === AudioType::Upload ||
                            ($record->type === AudioType::TTS && $record->tts_status !== AudioTTSStatus::Completed)),
                    ViewAction::make()
                        ->label('View Details'),
                    EditAction::make()
                        ->label('Edit Details'),
                    DeleteAction::make()
                        ->label('Delete Audio'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
