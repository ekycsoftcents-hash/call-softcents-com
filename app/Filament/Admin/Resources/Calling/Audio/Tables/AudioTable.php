<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Audio\Tables;

use App\Enums\AudioApproval;
use App\Enums\AudioConversionStatus;
use App\Enums\AudioTTSStatus;
use App\Enums\AudioType;
use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Jobs\ConvertAudioJob;
use App\Jobs\GenerateAudioJob;
use App\Models\Audio;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use LaraZeus\Tabler\Tabler;

final class AudioTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->url(fn (Audio $record) => CustomerResource::getUrl('view', ['record' => $record->user_id])),
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
                    ->badge()
                    ->placeholder('N/A'),
                TextColumn::make('duration')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => secondsToHuman((int) $state))
                    ->placeholder('N/A'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    MediaAction::make('converted_audio')
                        ->label('Play Converted')
                        ->icon(Tabler::Music)
                        ->media(fn (Audio $record) => $record->converted_url)
                        ->mediaType(MediaAction::TYPE_AUDIO)
                        ->autoplay()
                        ->visible(fn (Audio $record) => $record->conversion_status === AudioConversionStatus::Completed),
                    MediaAction::make('original_audio')
                        ->label('Play Original')
                        ->icon(Tabler::Music)
                        ->media(fn (Audio $record) => $record->original_url)
                        ->mediaType(MediaAction::TYPE_AUDIO)
                        ->autoplay()
                        ->visible(fn (Audio $record) => $record->original_path),
                ])
                    ->button()
                    ->outlined()
                    ->size(Size::ExtraSmall)
                    ->label('Play')
                    ->icon(Tabler::Music),
                ActionGroup::make([
                    Action::make('retry_conversion')
                        ->icon(Tabler::Refresh)
                        ->label('Retry')
                        ->action(function ($record) {
                            ConvertAudioJob::dispatch($record->id);
                        })
                        ->visible(fn (Audio $record) => $record->conversion_status === AudioConversionStatus::Failed),
                    Action::make('retry_tts')
                        ->icon(Tabler::Refresh)
                        ->label('Retry')
                        ->action(function ($record) {
                            GenerateAudioJob::dispatch($record->id);
                        })
                        ->visible(fn (Audio $record) => $record->tts_status === AudioTTSStatus::Failed),

                    Action::make('approve')
                        ->icon(Tabler::CircleCheck)
                        ->label('Approve')
                        ->requiresConfirmation()
                        ->visible(fn (Audio $record) => $record->approval !== AudioApproval::Approved)
                        ->action(fn (Audio $record) => $record->update(['approval' => AudioApproval::Approved])),
                    Action::make('reject')
                        ->icon(Tabler::CircleX)
                        ->label('Reject')
                        ->requiresConfirmation()
                        ->visible(fn (Audio $record) => $record->approval !== AudioApproval::Rejected)
                        ->action(fn (Audio $record) => $record->update(['approval' => AudioApproval::Rejected])),
                    Action::make('tts_retry')
                        ->icon(Tabler::Reload)
                        ->label('Retry TTS')
                        ->visible(fn (Audio $record) => $record->type === AudioType::TTS && $record->tts_status === AudioTTSStatus::Failed)
                        ->action(function (Audio $record) {
                            $data['tts_status'] = AudioTTSStatus::Pending;
                            $data['conversion_status'] = AudioConversionStatus::Pending;

                            $record->update($data);

                            Bus::chain([
                                new GenerateAudioJob($record->id),
                                new ConvertAudioJob($record->id),
                            ])->dispatch();
                        }),
                    ViewAction::make()
                        ->icon(Tabler::Eye)
                        ->label('View Details'),
                    DeleteAction::make()
                        ->icon(Tabler::Trash)
                        ->label('Delete Audio'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon(Tabler::CircleCheck)
                        ->action(function (Collection $records) {
                            $records->each(fn (Audio $record) => $record->update(['approval' => AudioApproval::Approved]));
                        })
                        ->successNotificationTitle('Selected audios have been approved.'),
                    BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon(Tabler::CircleX)
                        ->action(function (Collection $records) {
                            $records->each(fn (Audio $record) => $record->update(['approval' => AudioApproval::Rejected]));
                        })
                        ->successNotificationTitle('Selected audios have been rejected.'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
