<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Audio\Pages;

use App\Enums\AudioApproval;
use App\Filament\Admin\Resources\Calling\Audio\AudioResource;
use App\Models\Audio;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\Tabler\Tabler;

final class ViewAudio extends ViewRecord
{
    protected static string $resource = AudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon(Tabler::CircleCheck)
                ->visible(fn (Audio $record) => $record->approval === AudioApproval::Pending || $record->approval === AudioApproval::Rejected)
                ->requiresConfirmation()
                ->action(function (Audio $record) {
                    $record->update([
                        'approval' => AudioApproval::Approved,
                    ]);
                }),
            Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon(Tabler::CircleX)
                ->visible(fn (Audio $record) => $record->approval === AudioApproval::Pending || $record->approval === AudioApproval::Approved)
                ->requiresConfirmation()
                ->action(function (Audio $record) {
                    $record->update([
                        'approval' => AudioApproval::Rejected,
                    ]);
                }),
        ];
    }
}
