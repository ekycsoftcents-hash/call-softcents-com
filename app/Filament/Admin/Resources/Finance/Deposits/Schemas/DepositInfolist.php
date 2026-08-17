<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Deposits\Schemas;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Models\Deposit;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class DepositInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Deposit Details Section
                Section::make('Deposit Details')
                    ->description('Payment gateway and transaction information')
                    ->icon(Tabler::Cash)
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->icon(Tabler::CurrencyDollar)
                            ->numeric(decimalPlaces: 2),
                        TextEntry::make('currency')
                            ->label('Currency')
                            ->icon(Tabler::Coin)
                            ->badge(),
                        TextEntry::make('gateway')
                            ->label('Payment Gateway')
                            ->icon(Tabler::BuildingBank)
                            ->badge(),
                        TextEntry::make('transaction_id')
                            ->label('Transaction ID')
                            ->icon(Tabler::Hash)
                            ->fontFamily('mono')
                            ->copyable()
                            ->copyMessage('Transaction ID copied!')
                            ->copyMessageDuration(1500),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // Status & User Section
                Section::make('Status & User')
                    ->description('Deposit status and associated user')
                    ->icon(Tabler::InfoCircle)
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->icon(Tabler::CircleCheck)
                            ->badge()
                            ->color(fn ($state) => match ($state->value ?? $state) {
                                'completed', 'success' => 'success',
                                'pending' => 'warning',
                                'failed', 'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('user.name')
                            ->label('User')
                            ->icon(Tabler::User)
                            ->badge()
                            ->url(fn (Deposit $record) => $record->user_id ? CustomerResource::getUrl('view', ['record' => $record->user_id]) : null),
                    ])
                    ->columns()
                    ->collapsible(),

                // Meta Data Section
                Section::make('Meta Data')
                    ->description('Additional payment information')
                    ->icon(Tabler::FileText)
                    ->schema([
                        KeyValueEntry::make('meta_data')
                            ->label('Payment Details')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // System Information Section
                Section::make('System Information')
                    ->description('Record timestamps')
                    ->icon(Tabler::Clock)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->icon(Tabler::CalendarPlus)
                            ->since()
                            ->tooltip(fn (Deposit $record) => $record->created_at ? $record->created_at->format('M j, Y \a\t h:i A') : 'Unknown')
                            ->color('gray'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->icon(Tabler::Refresh)
                            ->since()
                            ->tooltip(fn (Deposit $record) => $record->updated_at ? $record->updated_at->format('M j, Y \a\t h:i A') : 'Never updated')
                            ->color('gray'),
                    ])
                    ->columns()
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
