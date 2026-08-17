<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Actions;

use LaraZeus\Tabler\Tabler;
use STS\FilamentImpersonate\Actions\Impersonate;

final class ImpersonateAction
{
    public static function make()
    {
        return Impersonate::make()
            ->icon(Tabler::Login2)
            ->label('Login as User')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Login as User')
            ->modalDescription('This action grants full access to another user account and is intended strictly for authorized debugging or support purposes. Never use it to impersonate a user without explicit permission.');
    }
}
