<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Finance\Deposits\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use LaraZeus\Tabler\Tabler;

final class DepositInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])->schema([
                    Group::make()
                        ->columnSpan(['md' => 2])
                        ->schema([
                            Section::make()
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('gateway')
                                            ->label('Gateway')
                                            ->badge()
                                            ->color('gray')
                                            ->icon(Tabler::BuildingBank),
                                        TextEntry::make('transaction_id')
                                            ->label('Transaction ID')
                                            ->fontFamily('mono')
                                            ->copyable(),
                                    ]),

                                    TextEntry::make('amount')
                                        ->label('Amount')
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->numeric(),

                                    KeyValueEntry::make('meta_data')
                                        ->label('Meta Data')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    Group::make()
                        ->columnSpan(['md' => 1])
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextEntry::make('status')
                                        ->badge(),
                                    TextEntry::make('user.name')
                                        ->label('User')
                                        ->icon(Tabler::User),
                                    TextEntry::make('currency')
                                        ->badge()
                                        ->color('gray'),
                                    TextEntry::make('created_at')
                                        ->label('Date')
                                        ->date()
                                        ->icon(Tabler::Calendar),
                                ]),
                        ]),
                ]),
            ]);
    }
}
