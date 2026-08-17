<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use LaraZeus\Tabler\Tabler;

enum UserNavigationGroup: string implements HasIcon, HasLabel
{
    case Audience = 'audience';
    case Calling = 'calling';
    case Finance = 'finance';
    case Developers = 'developers';

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
            self::Developers => Tabler::Code,
        };
    }
}
