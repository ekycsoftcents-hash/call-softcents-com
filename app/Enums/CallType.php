<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use LaraZeus\Tabler\Tabler;

enum CallType: string implements HasColor, HasIcon, HasLabel
{
    case Marketing = 'marketing';
    case OTP = 'otp';

    public function getColor(): string
    {
        return match ($this) {
            self::Marketing => 'primary',
            self::OTP => 'success',
        };
    }

    public function getIcon(): BackedEnum
    {
        return match ($this) {
            self::Marketing => Tabler::Speakerphone,
            self::OTP => Tabler::PasswordMobilePhone,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Marketing => 'Marketing',
            self::OTP => 'OTP',
        };
    }
}
