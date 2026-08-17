<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSArtists\Tables;

use App\Filament\Admin\Resources\TTS\TTSLanguages\TTSLanguageResource;
use App\Models\TTSArtist;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

final class TTSArtistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('ttsLanguage.name')
                    ->label('Language')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->url(fn (TTSArtist $record) => TTSLanguageResource::getUrl('view', ['record' => $record->tts_language_id])),
                TextColumn::make('name')
                    ->label('Artist Name')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->sortable(),
                IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('enable')
                        ->label('Enable selected')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['enabled' => true]))
                        ->color('success')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->successNotificationTitle('Selected artists have been enabled.'),
                    BulkAction::make('disable')
                        ->label('Disable selected')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['enabled' => false]))
                        ->color('danger')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->successNotificationTitle('Selected artists have been disabled.'),
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
