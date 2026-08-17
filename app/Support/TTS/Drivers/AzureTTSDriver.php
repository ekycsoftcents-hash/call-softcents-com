<?php

declare(strict_types=1);

namespace App\Support\TTS\Drivers;

use App\Support\TTS\Contracts\TTSDriver;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class AzureTTSDriver implements TTSDriver
{
    public function __construct(
        private string $key,
        private string $region
    ) {}

    public function speak(string $text, string $language, string $gender, string $artist, string $format = 'mp3'): string
    {
        $url = "https://{$this->region}.tts.speech.microsoft.com/cognitiveservices/v1";

        $ssml = <<<XML
<speak version='1.0' xml:lang='$language'>
    <voice xml:lang='$language' xml:gender='$gender' name='$artist'>
        {$text}
    </voice>
</speak>
XML;

        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->key,
            'X-Microsoft-OutputFormat' => 'audio-16khz-128kbitrate-mono-mp3',
            'User-Agent' => 'VoiceApp',
        ])
            ->withBody($ssml, 'application/ssml+xml')
            ->post($url);

        if ($response->failed()) {
            throw new RuntimeException("Azure TTS failed: {$response->body()}");
        }

        return $response->body();
    }
}
