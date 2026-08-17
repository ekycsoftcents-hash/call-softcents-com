<?php

declare(strict_types=1);

namespace App\Support\TTS\Drivers;

use App\Support\TTS\Contracts\TTSDriver;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class FrolaxTTSDriver implements TTSDriver
{
    public function __construct() {}

    /**
     * @throws ConnectionException
     */
    public function speak(string $text, string $language, string $gender, string $artist, string $format = 'mp3'): string
    {
        $url = config('services.frolax.tts.base_url');

        $response = Http::withToken(config('services.frolax.tts.api_key'))
            ->baseUrl($url)
            ->post('audio/speech', [
                'input' => $text,
                'voice' => $artist,
            ]);

        if ($response->failed()) {
            throw new RuntimeException("Frolax TTS failed: {$response->body()}");
        }

        return $response->body();
    }
}
