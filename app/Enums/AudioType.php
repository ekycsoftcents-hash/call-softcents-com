<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Collection;
use LaraZeus\Tabler\Tabler;

enum AudioType: string implements HasColor, HasIcon, HasLabel
{
    case TTS = 'tts';
    case Upload = 'upload';

    public static function availableOptions(UserAudioType $audioType): Collection
    {
        return (match ($audioType) {
            UserAudioType::TTS => collect([self::TTS]),
            UserAudioType::Upload => collect([self::Upload]),
            UserAudioType::Both => collect([self::TTS, self::Upload]),
        })->mapWithKeys(fn ($item) => [$item->value => $item->getLabel()]);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::TTS => 'Text to Speech',
            self::Upload => 'Upload Audio',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::TTS => 'success',
            self::Upload => 'primary',
        };
    }

    public function getIcon(): Tabler
    {
        return match ($this) {
            self::TTS => Tabler::Robot,
            self::Upload => Tabler::Microphone,
        };
    }
}
