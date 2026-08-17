<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use LaraZeus\Tabler\Tabler;

enum UserStatus: string implements HasIcon, HasLabel
{
    case Approved = 'approved';
    case Pending = 'pending';
    case Rejected = 'rejected';
    case Banned = 'banned';

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Approved => Tabler::CircleCheck,
            self::Pending => Tabler::HourglassLow,
            self::Rejected => Tabler::CircleX,
            self::Banned => Tabler::Ban,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }
}
