<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AudioConversionStatus;
use App\Models\Audio;
use Exception;
use FFMpeg\Format\Audio\Wav;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

final class ConvertAudioJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

    protected ?Audio $audio;

    /**
     * Create a new job instance.
     *
     * @param  int  $id  Audio ID
     */
    public function __construct(int $id)
    {
        $this->audio = Audio::query()->findOrFail($id);
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $this->audio->update([
            'conversion_status' => AudioConversionStatus::Processing,
            'conversion_error' => null,
        ]);

        try {
            $inputPath = $this->audio->original_path;
            $outputPath = 'audios/conversions/'.$this->audio->uuid.'.wav';

            // Validate input file exists
            if (! Storage::exists($inputPath)) {
                throw new Exception("Input file not found: {$inputPath}");
            }

            // Ensure output directory exists
            $this->ensureDirectoryExists('audios/conversions');

            // Convert audio
            $duration = $this->convertAudio($inputPath, $outputPath);

            // Update audio record
            $this->updateAudioRecord($outputPath, $duration);

        } catch (Exception $e) {
            // Mark audio as failed
            $this->audio->update([
                'conversion_status' => AudioConversionStatus::Failed,
                'conversion_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Exception $exception): void
    {
        Log::error('Audio conversion job failed permanently', [
            'audio_id' => $this->audio->id,
            'error' => $exception?->getMessage(),
        ]);

        $this->audio->update([
            'conversion_status' => AudioConversionStatus::Failed,
            'conversion_error' => $exception?->getMessage() ?? 'Unknown error',
        ]);
    }

    /**
     * Convert audio to Asterisk-compatible format.
     */
    protected function convertAudio(string $inputPath, string $outputPath): float
    {
        $ffmpeg = FFMpeg::fromDisk(config('filesystems.default'))->open($inputPath);

        // Get duration before conversion
        $duration = $ffmpeg->getDurationInSeconds();

        // Asterisk WAV format: 8kHz, 16-bit, mono, PCM
        $format = new Wav();
        $format->setAudioChannels(1);
        $format->setAudioKiloBitrate(128);

        $ffmpeg->export()
            ->inFormat($format)
            ->addFilter(['-ar', '8000'])           // 8kHz sample rate
            ->addFilter(['-ac', '1'])              // Mono
            ->addFilter(['-sample_fmt', 's16'])    // 16-bit signed PCM
            ->addFilter(['-acodec', 'pcm_s16le'])  // Ensure PCM codec
            ->toDisk(config('filesystems.default'))
            ->save($outputPath);

        return $duration;
    }

    /**
     * Update audio record with conversion details.
     */
    protected function updateAudioRecord(string $outputPath, float $duration): void
    {
        $size = Storage::size($outputPath);

        $this->audio->update([
            'converted_path' => $outputPath,
            'duration' => $duration,
            'size' => $size,
            'conversion_status' => AudioConversionStatus::Completed,
            'converted_at' => now(),
        ]);
    }

    /**
     * Ensure directory exists on the disk.
     */
    protected function ensureDirectoryExists(string $directory): void
    {
        if (! Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
    }
}
