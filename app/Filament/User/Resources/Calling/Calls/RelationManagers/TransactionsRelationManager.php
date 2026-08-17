<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Calls\RelationManagers;

use App\Enums\TransactionType;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll()
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency ?? 'BDT')
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(TransactionType::class)
                    ->searchable(),
            ])
            ->headerActions([
                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                // EditAction::make(),
                // DissociateAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DissociateBulkAction::make(),
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
