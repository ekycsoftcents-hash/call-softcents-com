<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSLanguages\Pages;

use App\Filament\Admin\Resources\TTS\TTSLanguages\TTSLanguageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

final class EditTTSLanguage extends EditRecord
{
    protected static string $resource = TTSLanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
