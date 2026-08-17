<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\Contact;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Propaganistas\LaravelPhone\PhoneNumber;

final class ContactImporter extends Importer
{
    protected static ?string $model = Contact::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('phone_number')
                ->label('Phone Number')
                ->requiredMapping()
                ->example(['8801322635808', '8801608460717'])
                ->rules(['required', 'max:255', 'phone:BD']),
        ];
    }

    public static function getChunkSize(): int
    {
        return 1000;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your contact import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    public function resolveRecord(): Contact|Model
    {
        $phone = rescue(fn () => new PhoneNumber($this->data['phone_number'], 'BD')->formatE164(), report: false);

        $this->data['phone_number'] = $phone;

        return Contact::firstOrNew([
            'group_id' => $this->options['group_id'],
            'phone_number' => $phone,
        ]);
    }

    public function getJobBatchSize(): ?int
    {
        return 500;
    }
}
