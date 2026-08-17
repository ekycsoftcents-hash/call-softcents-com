<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AudioTTSStatus;
use App\Models\Audio;
use App\Support\TTS\Contracts\TTSDriver;
use App\Support\TTS\TTSManager;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class GenerateAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    protected ?Audio $audio;

    public function __construct(public int $audioId)
    {
        $this->audio = Audio::find($this->audioId);
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(TTSManager $ttsManager): void
    {
        if (! $this->audio) {
            Log::warning('Audio not found for TTS processing', ['audio_id' => $this->audioId]);

            return;
        }

        $this->audio->load('ttsArtist.ttsLanguage');

        // Prevent reprocessing already completed audio
        if ($this->audio->tts_status === AudioTTSStatus::Completed) {
            Log::info('Audio already processed, skipping', ['audio_id' => $this->audioId]);

            return;
        }

        $this->audio->update(['tts_status' => AudioTTSStatus::Processing]);

        try {
            $this->validateAudioRelations($this->audio);

            $audioContent = $this->generateTTS($this->audio, $ttsManager);

            $filename = $this->saveAudioFile($this->audio, $audioContent);

            $this->updateAudioSuccess($this->audio, $filename);

            Log::info('TTS generated successfully', [
                'audio_id' => $this->audio->id,
                'filename' => $filename,
            ]);

        } catch (Throwable $e) {
            $this->handleFailure($this->audio, $e);
            throw $e;
        }
    }

    /**
     * Handle a job failure (called after all retries exhausted).
     */
    public function failed(?Throwable $exception): void
    {
        $audio = Audio::find($this->audioId);

        if ($audio) {
            Log::error('TTS job failed permanently', [
                'audio_id' => $this->audioId,
                'error' => $exception?->getMessage(),
            ]);

            $audio->update([
                'tts_status' => AudioTTSStatus::Failed,
                'tts_error' => $exception?->getMessage() ?? 'Unknown error after all retries',
            ]);
        }
    }

    /**
     * Determine if the job should be retried based on the exception.
     */
    public function shouldRetry(Throwable $exception): bool
    {
        // Don't retry validation errors
        if (str_contains($exception->getMessage(), 'has no')) {
            return false;
        }

        // Don't retry empty message errors
        if (str_contains($exception->getMessage(), 'no message text')) {
            return false;
        }

        // Retry other errors (network issues, API rate limits, etc.)
        return true;
    }

    /**
     * Validate that audio has all required relations.
     *
     * @throws Exception
     */
    protected function validateAudioRelations(Audio $audio): void
    {
        if (! $audio->ttsArtist) {
            throw new Exception('Audio has no TTS artist assigned');
        }

        if (! $audio->ttsArtist->ttsLanguage) {
            throw new Exception('TTS artist has no language assigned');
        }

        if (empty($audio->message)) {
            throw new Exception('Audio has no message text to convert');
        }
    }

    /**
     * Generate TTS audio content.
     */
    protected function generateTTS(Audio $audio, TTSManager $ttsManager): string
    {
        $artist = $audio->ttsArtist;
        $gender = $artist->gender;
        $language = $artist->ttsLanguage;
        $engine = $language->engine;

        /** @var TTSDriver $driver */
        $driver = $ttsManager->driver($engine->value);

        return $driver->speak(
            text: $audio->message,
            language: $language->code,
            gender: $gender->value,
            artist: $artist->code,
        );
    }

    /**
     * Save audio file to storage.
     *
     * @throws Exception
     */
    protected function saveAudioFile(Audio $audio, string $audioContent): string
    {
        $filename = 'audios/originals/'.$audio->uuid.'.mp3';

        // Ensure directory exists
        $directory = dirname($filename);
        if (! Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        // Validate audio content is not empty
        if (empty($audioContent)) {
            throw new Exception('Generated audio content is empty');
        }

        Storage::put($filename, $audioContent);

        // Verify file was saved successfully
        if (! Storage::exists($filename)) {
            throw new Exception('Failed to save audio file');
        }

        return $filename;
    }

    /**
     * Update audio record on success.
     */
    protected function updateAudioSuccess(Audio $audio, string $filename): void
    {
        $fileSize = Storage::size($filename);

        $audio->update([
            'original_path' => $filename,
            'tts_status' => AudioTTSStatus::Completed,
            'tts_generated_at' => now(),
            'size' => $fileSize,
            'tts_error' => null,
        ]);
    }

    /**
     * Handle job failure.
     */
    protected function handleFailure(Audio $audio, Throwable $e): void
    {
        Log::error('TTS generation failed', [
            'audio_id' => $audio->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        $audio->update([
            'tts_status' => AudioTTSStatus::Failed,
            'tts_error' => $e->getMessage(),
        ]);
    }
}
