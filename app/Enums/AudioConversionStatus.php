<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use LaraZeus\Tabler\Tabler;

enum AudioConversionStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processing => 'primary',
            self::Completed => 'success',
            self::Failed => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Pending,
            self::Processing => Tabler::Clock,
            self::Completed => Tabler::CircleCheck,
            self::Failed => Tabler::CircleX,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return str($this->name)->headline()->value();
    }
}
