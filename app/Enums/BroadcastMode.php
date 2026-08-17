<?php

declare(strict_types=1);

namespace App\Enums;

enum BroadcastMode: string
{
    case Voice = 'voice';
    case Ivr = 'ivr';

    public function label(): string
    {
        return match ($this) {
            self::Voice => 'Voice Broadcast',
            self::Ivr => 'IVR Broadcast',
        };
    }
}
