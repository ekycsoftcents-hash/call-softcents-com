<?php

declare(strict_types=1);

namespace App\Filament\User\Pages\Account;

use Filament\Panel;

final class Pending extends Page
{
    public static function getSlug(?Panel $panel = null): string
    {
        return 'account/pending';
    }

    public function getTitle(): string
    {
        return __('Account Pending Approval');
    }

    public function getHeading(): string
    {
        return __('Your account is pending approval');
    }

    public function getSubheading(): string
    {
        return __('Thank you for registering. Your account is currently under review. We will notify you once it has been approved.');
    }
}
