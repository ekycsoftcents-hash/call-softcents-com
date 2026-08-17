<?php

declare(strict_types=1);

namespace App\Support\TTS\Contracts;

interface TTSDriver
{
    /**
     * Convert text to speech.
     *
     * @return string Raw audio data
     */
    public function speak(string $text, string $language, string $gender, string $artist, string $format = 'mp3'): string;
}
