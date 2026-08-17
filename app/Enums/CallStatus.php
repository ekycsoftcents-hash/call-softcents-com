<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use LaraZeus\Tabler\Tabler;

enum CallStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Initiated = 'initiated';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Paused = 'paused';

    public function getLabel(): ?string
    {
        return str($this->name)->headline()->value();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending,
            self::Paused => 'gray',
            self::Initiated => 'warning',
            self::Processing => 'info',
            self::Completed => 'success',
            self::Failed => 'danger'
        };
    }

    public function getIcon(): Tabler
    {
        return match ($this) {
            self::Pending => Tabler::Clock,
            self::Initiated => Tabler::PlayerPlay,
            self::Processing => Tabler::PhoneCalling,
            self::Completed => Tabler::PhoneCheck,
            self::Failed => Tabler::CircleX,
            self::Paused => Tabler::PlayerPause
        };
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    public function isInitiated(): bool
    {
        return $this === self::Initiated;
    }

    public function isProcessing(): bool
    {
        return $this === self::Processing;
    }

    public function isCompleted(): bool
    {
        return $this === self::Completed;
    }

    public function isFailed(): bool
    {
        return $this === self::Failed;
    }

    public function isPaused(): bool
    {
        return $this === self::Paused;
    }

    public function isRefundable(): bool
    {
        return $this === self::Failed;
    }

    public function isPausable(): bool
    {
        return $this === self::Paused;
    }
}
