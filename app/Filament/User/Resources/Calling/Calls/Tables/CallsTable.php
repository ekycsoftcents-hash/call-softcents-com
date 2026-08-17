<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Calls\Tables;

use App\Enums\CallInterface;
use App\Enums\CallStatus;
use App\Enums\CallType;
use App\Filament\Exports\User\CallExporter;
use App\Models\Call;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

final class CallsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width(0)
                    ->alignCenter(),
                TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('interface')
                    ->label('From')
                    ->badge(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->numeric()
                    ->placeholder('-')
                    ->formatStateUsing(fn (float $state) => secondsToHuman($state))
                    ->sortable(),
                TextColumn::make('cost')
                    ->label('Cost')
                    ->money('BDT', decimalPlaces: 6)
                    ->sortable(),
                TextColumn::make('created_at'),
                TextColumn::make('updated_at'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(CallStatus::class)
                    ->searchable(),
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(CallType::class)
                    ->searchable(),
                SelectFilter::make('interface')
                    ->label('Interface')
                    ->options(CallInterface::class)
                    ->multiple()
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('retry')
                    ->label('Retry')
                    ->icon(Tabler::RepeatOnce)
                    ->color('danger')
                    ->visible(fn (Call $record) => $record->can_retry)
                    ->requiresConfirmation()
                    ->action(fn (Call $record) => $record->retry()),
            ]);
    }
}
