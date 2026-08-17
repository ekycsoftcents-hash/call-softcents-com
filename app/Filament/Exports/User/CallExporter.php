<?php

declare(strict_types=1);

namespace App\Filament\Exports\User;

use App\Models\Call;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class CallExporter extends Exporter
{
    protected static ?string $model = Call::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('phone_number')->label('Phone Number'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('type')->label('Type'),
            ExportColumn::make('interface')->label('Interface'),
            ExportColumn::make('duration')->label('Duration'),
            ExportColumn::make('cost')->label('Cost'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your call export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
