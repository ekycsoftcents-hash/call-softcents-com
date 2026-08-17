<?php

declare(strict_types=1);

namespace App\Filament\User\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

final class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::auth/pages/edit-profile.form.name.label'))
            ->disabled()
            ->dehydrated(false);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::auth/pages/edit-profile.form.email.label'))
            ->disabled()
            ->dehydrated(false);
    }
}
