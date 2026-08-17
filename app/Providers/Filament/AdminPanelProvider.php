<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard;
use App\Http\Middleware\AdminMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
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

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->brandLogoHeight('45px')
            ->brandLogo(url('logo-dark.svg'))
            ->darkModeBrandLogo(url('logo-white.svg'))
            ->favicon(url('favicon.png'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Financial')
                    ->icon(Heroicon::OutlinedCurrencyBangladeshi)
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Text to Speech')
                    ->icon(Heroicon::OutlinedMicrophone)
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon(Heroicon::OutlinedCog)
                    ->collapsible()
                    ->collapsed(),
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
                AdminMiddleware::class,
            ])
            ->viteTheme('resources/css/app.css')
            ->spa()
            ->unsavedChangesAlerts()
            ->renderHook(
                name: PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                hook: fn () => Blade::render("@livewire('server-actions')")
            );
    }
}
