<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use LaraZeus\Tabler\Tabler;

enum TTSEngine: string implements HasColor, HasIcon, HasLabel
{
    case Azure = 'azure';
    case Frolax = 'frolax';

    public function getLabel(): string
    {
        return match ($this) {
            self::Azure => 'Azure',
            self::Frolax => 'Frolax',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Azure => 'primary',
            self::Frolax => 'success',
        };
    }

    public function getIcon(): BackedEnum
    {
        return match ($this) {
            self::Azure => Tabler::BrandWindows,
            self::Frolax => Tabler::Server,
        };
    }
}
