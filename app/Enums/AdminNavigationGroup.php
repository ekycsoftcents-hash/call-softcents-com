<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use LaraZeus\Tabler\Tabler;

enum AdminNavigationGroup: string implements HasIcon, HasLabel
{
    case Audience = 'audience';
    case Calling = 'calling';
    case Finance = 'finance';
    case TextToSpeech = 'text-to-speech';
    case Settings = 'settings';

    public function getLabel(): string|Htmlable|null
    {
        return str($this->name)->headline()->value();
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Audience => Tabler::Users,
            self::Calling => Tabler::Phone,
            self::Finance => Tabler::CurrencyDollar,
            self::TextToSpeech => Tabler::Speakerphone,
            self::Settings => Tabler::Settings,
        };
    }
}
