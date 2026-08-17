<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use LaraZeus\Tabler\Tabler;

enum TTSArtistGender: string implements HasColor, HasIcon, HasLabel
{
    case Male = 'male';
    case Female = 'female';
    case Neutral = 'neutral';

    public function getLabel(): string
    {
        return str($this->name)->headline()->value();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Male => 'info',
            self::Female => 'primary',
            self::Neutral => 'gray',
        };
    }

    public function getIcon(): Tabler
    {
        return match ($this) {
            self::Male => Tabler::Man,
            self::Female => Tabler::Woman,
            self::Neutral => Tabler::GenderGenderless,
        };
    }
}
