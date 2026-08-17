<?php

declare(strict_types=1);

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

function secondsToHuman(int|float $seconds): string
{
    return rescue(function () use ($seconds) {
        return CarbonInterval::seconds($seconds)->cascade()->forHumans();
    }, 'N/A');
}

function bytesToHuman(int $bytes): string
{
    return rescue(function () use ($bytes) {
        return Number::fileSize($bytes);
    }, 'N/A');
}

function getFileUrl(?string $path, $expires = 3600, $ts = false): ?string
{
    if ($ts) {
        ray($path)->showApp();
    }
    if (empty($path)) {
        return null;
    }

    $storage = Storage::disk(config('filesystems.default'));

    if (! $storage->exists($path)) {
        return '';
    }

    if (config('filesystems.default') === 'public') {
        return $storage->url($path);
    }

    return $storage->temporaryUrl('audios/originals/01KMYWAHZJZR08RQR53JD4FP52.wav', now()->addSeconds($expires));
}
