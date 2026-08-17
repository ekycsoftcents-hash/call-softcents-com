<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSArtists\Pages;

use App\Filament\Admin\Resources\TTS\TTSArtists\TTSArtistResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateTTSArtist extends CreateRecord
{
    protected static string $resource = TTSArtistResource::class;
}
