<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Transactions\Schemas;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Models\Transaction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Transaction Details Section
                Section::make('Transaction Details')
                    ->description('Core transaction information')
                    ->icon(Tabler::Receipt)
                    ->schema([
                        TextEntry::make('type')
                            ->label('Type')
                            ->icon(Tabler::Tag)
                            ->badge()
                            ->color(fn ($state) => match ($state->value ?? $state) {
                                'credit' => 'success',
                                'debit' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->icon(Tabler::CurrencyDollar)
                            ->numeric(decimalPlaces: 2)
                            ->color(fn ($record) => $record->amount > 0 ? 'success' : ($record->amount < 0 ? 'danger' : 'gray')),
                        TextEntry::make('currency')
                            ->label('Currency')
                            ->icon(Tabler::Coin)
                            ->badge(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('No description')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // User & Reference Section
                Section::make('User & Reference')
                    ->description('Associated user and reference information')
                    ->icon(Tabler::Link)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User')
                            ->icon(Tabler::User)
                            ->badge()
                            ->url(fn (Transaction $record) => $record->user_id ? CustomerResource::getUrl('view', ['record' => $record->user_id]) : null),
                        TextEntry::make('transactionable_type')
                            ->label('Reference Type')
                            ->icon(Tabler::FileText)
                            ->formatStateUsing(fn ($state) => $state ? class_basename($state) : null)
                            ->placeholder('No reference'),
                        TextEntry::make('transactionable_id')
                            ->label('Reference ID')
                            ->icon(Tabler::Hash)
                            ->numeric()
                            ->placeholder('No reference'),
                    ])
                    ->columns()
                    ->collapsible(),

                // System Information Section
                Section::make('System Information')
                    ->description('Record timestamps')
                    ->icon(Tabler::InfoCircle)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->icon(Tabler::CalendarPlus)
                            ->since()
                            ->tooltip(fn (Transaction $record) => $record->created_at ? $record->created_at->format('M j, Y \a\t h:i A') : 'Unknown')
                            ->color('gray'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->icon(Tabler::Refresh)
                            ->since()
                            ->tooltip(fn (Transaction $record) => $record->updated_at ? $record->updated_at->format('M j, Y \a\t h:i A') : 'Never updated')
                            ->color('gray'),
                    ])
                    ->columns()
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
