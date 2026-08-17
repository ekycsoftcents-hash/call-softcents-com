<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class CallingSetting extends Settings
{
    public int $pulse_duration = 10;

    public float $pulse_rate = 0.10;

    public int $max_retry_attempts = 3;

    public float $campaign_success_threshold = 1.0;

    public static function group(): string
    {
        return 'calling';
    }
}
