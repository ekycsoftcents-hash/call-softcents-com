<?php

declare(strict_types=1);

use App\Enums\TTSEngine;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('tts.engine', TTSEngine::Azure);
    }
};
