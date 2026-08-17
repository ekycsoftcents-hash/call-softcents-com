<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\CallStatus;
use App\Enums\CampaignStatus;
use App\Enums\UserType;
use App\Models\Call;
use App\Models\Campaign;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use LaraZeus\Tabler\Tabler;

final class StatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected static bool $isLazy = true;

    public function getColumns(): int|array
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::whereType(UserType::User)->count())
                ->description('Active users on the platform')
                ->icon(Tabler::Users),

            Stat::make('Total Calls', function () {
                $count = Call::count();

                return Number::abbreviate($count, maxPrecision: 2);
            })
                ->description('Calls made today')
                ->icon(Tabler::PhoneCalling),

            Stat::make('Revenue', function () {
                $cost = Call::whereStatus(CallStatus::Completed)->sum('cost');

                return Number::currency((float) $cost, 'BDT');
            })
                ->description('Total revenue generated')
                ->icon(Tabler::Businessplan),

            Stat::make('Total User Balance', function () {
                $balance = User::whereType(UserType::User)
                    ->sum('balance');

                return Number::currency((float) $balance, 'BDT');
            })
                ->description('Balance across all users')
                ->icon(Tabler::Wallet),

            Stat::make('Total Campaigns', Campaign::count())
                ->description('All time total campaigns')
                ->icon(Tabler::Speakerphone),
        ];
    }
}
