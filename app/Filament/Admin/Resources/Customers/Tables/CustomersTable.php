<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Tables;

use App\Enums\UserStatus;
use App\Filament\Admin\Resources\Customers\Actions\ApprovalAction;
use App\Filament\Admin\Resources\Customers\Actions\DepositAction;
use App\Filament\Admin\Resources\Customers\Actions\ImpersonateAction;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

final class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('balance')
                    ->label('Balance')
                    ->searchable()
                    ->sortable()
                    ->money('BDT')
                    ->alignCenter(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ApprovalAction::make()
                    ->hidden(fn (User $record) => $record->status !== UserStatus::Pending),
                ActionGroup::make([
                    ViewAction::make()->label('View Details'),
                    DepositAction::make(),
                    EditAction::make()->label('Edit Customer'),
                    ImpersonateAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
