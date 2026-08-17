<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use LaraZeus\Tabler\Tabler;

enum UserAudioType: string implements HasColor, HasIcon, HasLabel
{
    case Upload = 'upload';
    case TTS = 'tts';
    case Both = 'both';

    public function getLabel(): string
    {
        return match ($this) {
            self::Upload => 'Upload',
            self::TTS => 'TTS',
            self::Both => 'Both',
        };
    }

    public function getIcon(): Tabler
    {
        return match ($this) {
            self::Upload => Tabler::FileUpload,
            self::TTS => Tabler::Speakerphone,
            self::Both => Tabler::SettingsAutomation,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Upload => 'success',
            self::TTS => 'primary',
            self::Both => 'warning',
        };
    }
}
