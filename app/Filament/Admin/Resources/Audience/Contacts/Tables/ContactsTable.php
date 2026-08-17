<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Contacts\Tables;

use App\Filament\Admin\Resources\Audience\Groups\GroupResource;
use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Filament\Exports\ContactExporter;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

final class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('group.user.name')
                    ->label('Customer')
                    ->searchable()
                    ->url(fn ($record) => CustomerResource::getUrl('view', ['record' => $record->group->user_id])),
                TextColumn::make('group.name')
                    ->searchable()
                    ->url(fn ($record) => GroupResource::getUrl('view', ['record' => $record->group_id])),
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
                ExportAction::make('export')
                    ->label('Export')
                    ->icon(Tabler::TableExport)
                    ->exporter(ContactExporter::class)
                    ->columnMappingColumns(3),
            ]);
    }
}
