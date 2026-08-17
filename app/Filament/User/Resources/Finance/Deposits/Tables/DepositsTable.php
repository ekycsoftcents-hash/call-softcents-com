<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Finance\Deposits\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class DepositsTable
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
                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('Currency')
                    ->searchable(),
                TextColumn::make('gateway')
                    ->label('Gateway')
                    ->searchable(),
                TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
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
            ]);
    }
}
