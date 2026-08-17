<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Campaigns\Tables;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', direction: 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width(0)
                    ->alignCenter(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('audio.title')
                    ->label('Audio')
                    ->limit(20),
                TextColumn::make('group.name')
                    ->label('Group')
                    ->limit(20),
                TextColumn::make('approval')
                    ->label('Approval')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Current Status')
                    ->badge(),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled At')
                    ->placeholder('Not Scheduled')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at'),
                TextColumn::make('updated_at'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn (Campaign $record) => $record->status === CampaignStatus::Pending && $record->scheduled_at),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
