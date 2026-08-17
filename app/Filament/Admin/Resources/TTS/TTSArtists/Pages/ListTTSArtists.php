<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSArtists\Pages;

use App\Filament\Admin\Resources\TTS\TTSArtists\TTSArtistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListTTSArtists extends ListRecords
{
    protected static string $resource = TTSArtistResource::class;

    protected static ?string $title = 'Artists';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Artist'),
        ];
    }
}
