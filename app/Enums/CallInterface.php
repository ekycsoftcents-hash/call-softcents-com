<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use LaraZeus\Tabler\Tabler;

enum CallInterface: string implements HasColor, HasIcon, HasLabel
{
    case Web = 'web';
    case Mobile = 'mobile';
    case API = 'api';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Web => 'primary',
            self::Mobile => 'success',
            self::API => 'warning',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Web => Tabler::DeviceLaptop,
            self::Mobile => Tabler::DeviceMobile,
            self::API => Tabler::Api,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }
}
