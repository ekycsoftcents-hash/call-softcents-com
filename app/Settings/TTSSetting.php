<?php

declare(strict_types=1);

namespace App\Settings;

use App\Enums\TTSEngine;
use Spatie\LaravelSettings\Settings;

final class TTSSetting extends Settings
{
    public TTSEngine $engine;

    public static function group(): string
    {
        return 'tts';
    }
}
