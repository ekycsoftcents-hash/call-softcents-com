<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Calls\Pages;

use App\Actions\CreateNewCall;
use App\Exceptions\BusinessException;
use App\Filament\User\Resources\Calling\Calls\CallResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Throwable;

final class CreateCall extends CreateRecord
{
    protected static string $resource = CallResource::class;

    /**
     * @throws Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        try {
            $action = app(CreateNewCall::class);

            return $action->handle(auth()->user(), $data);

        } catch (Throwable $e) {
            Notification::make()
                ->title('Call Creation Failed')
                ->body($e instanceof BusinessException ? $e->getMessage() : 'Something went wrong while creating the call. Please try again.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
