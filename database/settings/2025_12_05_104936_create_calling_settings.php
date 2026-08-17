<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('calling.pulse_rate', 0.50);
        $this->migrator->add('calling.pulse_duration', 10);
    }
};
