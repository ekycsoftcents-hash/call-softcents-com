<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

final class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    protected bool $persistsFiltersInSession = true;

    //    public function getHeaderActions(): array
    //    {
    //        return [
    //            FilterAction::make()
    //                ->schema([
    //                    Select::make('date_range')
    //                        ->label('Date Range')
    //                        ->options([
    //                            'today' => 'Today',
    //                            'yesterday' => 'Yesterday',
    //                            'week' => 'Last Week',
    //                            'month' => 'Last Month',
    //                            'year' => 'This Year',
    //                        ]),
    //                ]),
    //        ];
    //    }
}
