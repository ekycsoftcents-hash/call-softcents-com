<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSArtists\Pages;

use App\Filament\Admin\Resources\TTS\TTSArtists\TTSArtistResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditTTSArtist extends EditRecord
{
    protected static string $resource = TTSArtistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
