<?php

declare(strict_types=1);

namespace App\Filament\User\Pages\Account;

use Filament\Panel;

final class Rejected extends Page
{
    public static function getSlug(?Panel $panel = null): string
    {
        return 'account/rejected';
    }

    public function getTitle(): string
    {
        return __('Account Rejected');
    }

    public function getHeading(): string
    {
        return __('Your account has been rejected');
    }

    public function getSubheading(): string
    {
        return __('We regret to inform you that your account application has been rejected. For more information, please contact support.');
    }
}
