<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSLanguages\Pages;

use App\Filament\Admin\Resources\TTS\TTSLanguages\TTSLanguageResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateTTSLanguage extends CreateRecord
{
    protected static string $resource = TTSLanguageResource::class;

    protected static ?string $title = 'Add Language';
}
