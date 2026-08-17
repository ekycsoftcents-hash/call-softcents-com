<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Deposits\Tables;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class DepositsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user:id,name'))
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->url(fn ($record) => CustomerResource::getUrl('edit', ['record' => $record->user_id])),
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
