<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Enums\UserNavigationGroup;
use App\Filament\User\Pages\Account\Banned;
use App\Filament\User\Pages\Account\Pending;
use App\Filament\User\Pages\Account\Rejected;
use App\Filament\User\Pages\Dashboard;
use App\Filament\User\Pages\EditProfile;
use App\Filament\User\Pages\Register;
use App\Http\Middleware\HandleUserStatus;
use App\Http\Middleware\UserMiddleware;
use App\Models\ResellerBranding;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('')
            ->login()
            ->profile(EditProfile::class)
            ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->databaseNotifications()
            ->emailChangeVerification()
            ->sidebarCollapsibleOnDesktop()
            ->favicon(fn () => app()->bound(ResellerBranding::class) && app(ResellerBranding::class)->favicon_url
                ? app(ResellerBranding::class)->favicon_url
                : url('favicon.png'))
            ->brandName(fn () => app()->bound(ResellerBranding::class)
                ? app(ResellerBranding::class)->brand_name
                : config('app.name'))
            ->brandLogoHeight('45px')
            ->brandLogo(fn () => app()->bound(ResellerBranding::class) && app(ResellerBranding::class)->logo_url
                ? app(ResellerBranding::class)->logo_url
                : url('logo-dark.svg'))
            ->darkModeBrandLogo(fn () => app()->bound(ResellerBranding::class) && app(ResellerBranding::class)->logo_url
                ? app(ResellerBranding::class)->logo_url
                : url('logo-white.svg'))
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\Filament\User\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\Filament\User\Pages')
            ->pages([
                Dashboard::class,
                Pending::class,
                Rejected::class,
                Banned::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\Filament\User\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Financial')
                    ->icon(Heroicon::OutlinedCreditCard)
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Developers')
                    ->icon(Heroicon::OutlinedCodeBracket)
                    ->collapsible()
                    ->collapsed(),
            ])
            ->navigationItems([
                NavigationItem::make('docs')
                    ->group(UserNavigationGroup::Developers)
                    ->label('Documentation')
                    ->url('/docs', true)
                    ->icon('heroicon-o-book-open')
                    ->sort(2),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                UserMiddleware::class,
                HandleUserStatus::class,
            ])
            ->renderHook(
                name: PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                hook: fn (): string => Blade::render('@livewire(\'user-balance\')')
            )
            ->viteTheme('resources/css/app.css')
            ->spa()
            ->databaseTransactions()
            ->unsavedChangesAlerts();
    }
}
