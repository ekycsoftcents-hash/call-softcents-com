<?php

declare(strict_types=1);

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

final class ResellerPortal extends Page
{
    protected static ?string $navigationLabel = 'Reseller Portal';

    protected static ?string $title = 'Reseller Portal';

    protected static ?string $slug = 'reseller-portal';

    protected static string $view = 'filament.user.pages.reseller-portal';

    public static function canAccess(): bool
    {
        return auth()->user()?->isReseller() === true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isReseller() === true;
    }
}
