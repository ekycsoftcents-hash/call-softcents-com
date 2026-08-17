<?php

declare(strict_types=1);

namespace App\Filament\User\Pages\Account;

use Filament\Panel;

final class Banned extends Page
{
    public static function getSlug(?Panel $panel = null): string
    {
        return 'account/banned';
    }

    public function getTitle(): string
    {
        return __('Account Banned');
    }

    public function getHeading(): string
    {
        return __('Your account has been banned');
    }

    public function getSubheading(): string
    {
        return __('Your account has been banned due to violations of our Terms & Conditions. If you believe this is a mistake, please contact support for further assistance.');
    }
}
