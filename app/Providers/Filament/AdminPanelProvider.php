<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->spa()
            ->brandName(fn () => Setting::getValue('site_name', config('app.name')))
            ->brandLogo(fn () => view('components.filament-logo'))
            ->favicon(fn () => Setting::getValue('site_favicon') ? Storage::url(Setting::getValue('site_favicon')) : asset('favicon.ico'))
            ->globalSearch(false)
            ->maxContentWidth(Width::Full)
            ->sidebarWidth('16rem')
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Olive,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->plugin(FilamentShieldPlugin::make())
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
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style>
                    .fi-header-heading {
                        display: none !important;
                    }
                    .fi-sidebar,
                    .fi-sidebar-nav,
                    .fi-sidebar-content,
                    [data-fi-sidebar] {
                        scrollbar-width: none !important;
                        -ms-overflow-style: none !important;
                    }
                    .fi-sidebar::-webkit-scrollbar,
                    .fi-sidebar-nav::-webkit-scrollbar,
                    .fi-sidebar-content::-webkit-scrollbar,
                    [data-fi-sidebar]::-webkit-scrollbar {
                        display: none !important;
                        width: 0 !important;
                        height: 0 !important;
                    }
                </style>',
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => new HtmlString(view('filament.hooks.order-receipt-script')->render()),
            );

    }
}
