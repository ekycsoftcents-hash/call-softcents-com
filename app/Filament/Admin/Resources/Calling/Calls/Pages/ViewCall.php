<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Calls\Pages;

use App\Exceptions\BusinessException;
use App\Filament\Admin\Resources\Calling\Calls\CallResource;
use App\Models\Call;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\Tabler\Tabler;

final class ViewCall extends ViewRecord
{
    protected static string $resource = CallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry')
                ->label('Retry')
                ->icon(Tabler::RepeatOnce)
                ->color('danger')
                ->visible(fn (Call $record) => $record->can_retry)
                ->requiresConfirmation()
                ->action(function (Call $record): void {
                    try {
                        $record->retry();
                    } catch (BusinessException $e) {
                        Notification::make()
                            ->title('Retry Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
