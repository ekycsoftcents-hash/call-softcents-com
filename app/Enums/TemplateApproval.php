<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use LaraZeus\Tabler\Tabler;

enum TemplateApproval: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return str($this->name)->headline()->value();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Pending => 'warning',
        };
    }

    public function getIcon(): Tabler
    {
        return match ($this) {
            self::Approved => Tabler::CircleCheck,
            self::Rejected => Tabler::CircleX,
            self::Pending => Tabler::Clock,
        };
    }
}
