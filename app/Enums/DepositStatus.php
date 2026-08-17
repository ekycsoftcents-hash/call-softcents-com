<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum DepositStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            DepositStatus::Completed => 'success',
            DepositStatus::Pending => 'warning',
            DepositStatus::Failed => 'danger',
            DepositStatus::Refunded => 'gray',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            DepositStatus::Completed => Heroicon::OutlinedCheckCircle,
            DepositStatus::Pending => Heroicon::OutlinedClock,
            DepositStatus::Failed => Heroicon::OutlinedXCircle,
            DepositStatus::Refunded => Heroicon::OutlinedArrowUturnLeft,
        };
    }

    public function isCompleted(): bool
    {
        return $this === DepositStatus::Completed;
    }
}
