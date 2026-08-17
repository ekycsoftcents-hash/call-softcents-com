<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Groups\Pages;

use App\Filament\User\Resources\Audience\Groups\GroupResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

final class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return auth()->user()->groups()->create($data);
    }
}
