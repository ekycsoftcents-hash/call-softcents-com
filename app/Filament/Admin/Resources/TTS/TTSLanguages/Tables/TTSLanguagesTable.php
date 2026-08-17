<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSLanguages\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

final class TTSLanguagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Language Name')
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable(),
                TextColumn::make('engine')
                    ->label('Engine')
                    ->searchable()
                    ->badge(),
                TextColumn::make('tts_artists_count')
                    ->label('Artists')
                    ->counts('ttsArtists')
                    ->badge()
                    ->alignCenter(),
                IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->alignCenter(),
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
                SelectFilter::make('engine')
                    ->label('Engine')
                    ->searchable()
                    ->options([
                        'azure' => 'Azure',
                        'frolax' => 'Frolax',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
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
                        ->successNotificationTitle('Selected languages have been enabled.'),
                    BulkAction::make('disable')
                        ->label('Disable selected')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['enabled' => false]))
                        ->color('danger')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->successNotificationTitle('Selected languages have been disabled.'),
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
