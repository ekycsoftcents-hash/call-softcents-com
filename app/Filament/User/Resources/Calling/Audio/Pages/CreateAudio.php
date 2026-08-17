<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Audio\Pages;

use App\Filament\User\Resources\Calling\Audio\AudioResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

final class CreateAudio extends CreateRecord
{
    protected static string $resource = AudioResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        unset($data['gender']);

        return auth()->user()->audio()->create($data);
    }
}
