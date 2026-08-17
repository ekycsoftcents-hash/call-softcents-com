<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Finance\Transactions\Schemas;

use App\Filament\User\Resources\Calling\Calls\CallResource;
use App\Filament\User\Resources\Finance\Deposits\DepositResource;
use App\Models\Call;
use App\Models\Deposit;
use App\Models\Transaction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use LaraZeus\Tabler\Tabler;

final class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->columnSpan(2)
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('type')
                                                ->badge(),
                                            TextEntry::make('created_at')
                                                ->badge()
                                                ->date()
                                                ->color('gray')
                                                ->icon(Tabler::Calendar),
                                        ]),

                                        TextEntry::make('amount')
                                            ->label('Amount')
                                            ->size(TextSize::Large)
                                            ->weight(FontWeight::Bold)
                                            ->money('BDT', decimalPlaces: 6),

                                        TextEntry::make('description')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label('User')
                                            ->icon(Tabler::User),
                                        TextEntry::make('transactionable_type')
                                            ->label('Transaction Type')
                                            ->formatStateUsing(fn (string $state) => class_basename($state))
                                            ->placeholder('-'),
                                        TextEntry::make('transactionable_id')
                                            ->label('Transaction ID')
                                            ->numeric()
                                            ->url(function (Transaction $record) {
                                                if ($record->transactionable_type === Deposit::class && $record->transactionable) {
                                                    return DepositResource::getUrl('view', ['record' => $record->transactionable_id]);
                                                }
                                                if ($record->transactionable_type === Call::class && $record->transactionable) {
                                                    return CallResource::getUrl('view', ['record' => $record->transactionable_id]);
                                                }

                                                return null;
                                            })
                                            ->placeholder('-'),
                                        TextEntry::make('currency')
                                            ->badge()
                                            ->color('gray'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
