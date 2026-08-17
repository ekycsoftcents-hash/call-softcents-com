<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Finance\Transactions\Tables;

use App\Enums\TransactionType;
use App\Filament\User\Resources\Finance\Deposits\DepositResource;
use App\Models\Call;
use App\Models\Deposit;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', direction: 'desc')
            ->poll('5s')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width(0)
                    ->alignCenter(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->prefix('৳ ')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('transactionable_type')
                    ->label('Reference')
                    ->searchable()
                    ->formatStateUsing(fn (Transaction $record) => match ($record->transactionable_type) {
                        Deposit::class => 'Deposit',
                        Call::class => 'Call',
                        default => 'N/A',
                    }),
                TextColumn::make('transactionable_id')
                    ->label('Reference ID')
                    ->numeric()
                    ->sortable()
                    ->url(fn (Transaction $record) => match ($record->transactionable_type) {
                        Deposit::class => $record->transactionable ? DepositResource::getUrl('view', ['record' => $record->transactionable_id]) : 'javascript:void(0);',
                        default => null,
                    }),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(TransactionType::class)
                    ->searchable(),
                SelectFilter::make('transactionable_type')
                    ->label('Reference Type')
                    ->options([
                        Deposit::class => 'Deposit',
                        Call::class => 'Call',
                    ])
                    ->searchable(),
            ]);
    }
}
