<?php

declare(strict_types=1);

namespace App\Support\TTS;

use App\Support\TTS\Contracts\TTSDriver;
use App\Support\TTS\Drivers\AzureTTSDriver;
use App\Support\TTS\Drivers\FrolaxTTSDriver;
use Illuminate\Support\Manager;

final class TTSManager extends Manager
{
    public function getDefaultDriver()
    {
        return $this->config->get('tts.default', 'azure');
    }

    public function createAzureDriver(): TTSDriver
    {
        return new AzureTTSDriver(
            $this->config->get('services.azure.tts.key'),
            $this->config->get('services.azure.tts.region')
        );
    }

    public function createFrolaxDriver(): TTSDriver
    {
        return new FrolaxTTSDriver();
    }
}
