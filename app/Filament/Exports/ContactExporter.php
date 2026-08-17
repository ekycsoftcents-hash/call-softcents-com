<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Contact;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class ContactExporter extends Exporter
{
    protected static ?string $model = Contact::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID')
                ->enabledByDefault(false),
            ExportColumn::make('phone_number'),
            ExportColumn::make('created_at')
                ->enabledByDefault(false),
            ExportColumn::make('updated_at')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your contact export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
