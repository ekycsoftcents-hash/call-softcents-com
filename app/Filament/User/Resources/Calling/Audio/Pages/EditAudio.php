<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Audio\Pages;

use App\Filament\User\Resources\Calling\Audio\AudioResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

final class EditAudio extends EditRecord
{
    protected static string $resource = AudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            'title' => $data['title'] ?? 'Untitled Audio',
            'description' => $data['description'] ?? null,
        ];
    }
}
