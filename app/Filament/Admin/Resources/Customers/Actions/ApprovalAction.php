<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Actions;

use App\Enums\UserStatus;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use LaraZeus\Tabler\Tabler;

final class ApprovalAction
{
    public static function make(): ActionGroup
    {
        return ActionGroup::make([
            self::getApproveAction(),
            self::getRejectAction(),
            self::getBannedAction(),
        ])
            ->button()
            ->color('danger')
            ->icon(Tabler::UserCheck)
            ->label(__('Approval'))
            ->outlined();
    }

    private static function getApproveAction(): Action
    {
        return Action::make('approve')
            ->icon(Tabler::CircleCheck)
            ->label(__('Approve'))
            ->color('primary')
            ->requiresConfirmation()
            ->modalDescription(__('Are you sure you want to approve this user?'))
            ->action(function (User $record) {
                $record->update(['status' => UserStatus::Approved]);

                Notification::make()
                    ->title(__('User approved successfully.'))
                    ->success()
                    ->send();
            })
            ->visible(fn (User $record) => $record->status !== UserStatus::Approved);
    }

    private static function getRejectAction(): Action
    {
        return Action::make('reject')
            ->icon(Tabler::CircleX)
            ->label(__('Reject'))
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription(__('Are you sure you want to reject this user?'))
            ->action(function (User $record) {
                $record->update(['status' => UserStatus::Rejected]);

                Notification::make()
                    ->title(__('User rejected successfully.'))
                    ->success()
                    ->send();
            })
            ->visible(fn (User $record) => $record->status !== UserStatus::Rejected);
    }

    private static function getBannedAction(): Action
    {
        return Action::make('ban')
            ->icon(Tabler::UserX)
            ->label(__('Ban'))
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription(__('Are you sure you want to ban this user?'))
            ->action(function (User $record) {
                $record->update(['status' => UserStatus::Banned]);

                Notification::make()
                    ->title(__('User banned successfully.'))
                    ->success()
                    ->send();
            })
            ->visible(fn (User $record) => $record->status !== UserStatus::Banned);
    }
}
