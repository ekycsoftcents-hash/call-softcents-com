<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Contacts\Tables;

use App\Filament\User\Resources\Audience\Groups\GroupResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ContactsTable
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
                TextColumn::make('group.name')
                    ->searchable()
                    ->url(fn ($record) => GroupResource::getUrl('edit', ['record' => $record->group_id])),
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
