<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSLanguages\Pages;

use App\Filament\Admin\Resources\TTS\TTSLanguages\TTSLanguageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewTTSLanguage extends ViewRecord
{
    protected static string $resource = TTSLanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
