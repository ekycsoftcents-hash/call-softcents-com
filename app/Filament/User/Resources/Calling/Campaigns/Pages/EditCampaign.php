<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Campaigns\Pages;

use App\Filament\User\Resources\Calling\Campaigns\CampaignResource;
use App\Models\Campaign;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

final class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    /* @var  $record Campaign */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($record->scheduled_at === null) {
            Notification::make()
                ->title('Cannot edit a campaign that is not pending and has no scheduled time.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'scheduled_at' => 'Cannot edit a campaign that is not pending and has no scheduled time.',
            ]);
        }

        if ($record->scheduled_at->isPast()) {
            Notification::make()
                ->title('Cannot edit a campaign that is not pending and already scheduled in the past.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'scheduled_at' => 'Cannot edit a campaign that is not pending and already scheduled in the past.',
            ]);
        }

        return parent::handleRecordUpdate($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
