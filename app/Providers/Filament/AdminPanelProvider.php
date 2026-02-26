<?php

namespace App\Providers\Filament;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Resources\AdminResource\Widgets\SystemResource;
use App\Filament\Resources\PelangganResource;
use App\Filament\Resources\PelangganResource\Widgets\PelangganOverview;
use App\Livewire\ServerInfo;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Vite;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id("admin")
            ->path("admin")
            ->login()
            ->brandName("Interdesa Pecangaan")
            ->brandLogo(asset("interdesa.png"))
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => Blade::render(
                    "@livewire(\App\Livewire\ServerIndicator::class)",
                ),
            )
            ->assets([
                Css::make(
                    "my-stylesheet",
                    Vite::asset("resources/css/app.css"),
                ),
            ])
            ->colors([
                "primary" => Color::Blue,
            ])
            ->discoverResources(
                in: app_path("Filament/Resources"),
                for: "App\\Filament\\Resources",
            )
            ->discoverPages(
                in: app_path("Filament/Pages"),
                for: "App\\Filament\\Pages",
            )
            ->discoverClusters(
                in: app_path("Filament/Clusters"),
                for: "App\\Filament\\Clusters",
            )
            ->plugins([
                // FilamentUsersPlugin::make(),
                // FilamentShieldPlugin::make()
            ])
            ->pages([Dashboard::class])
            ->discoverWidgets(
                in: app_path("Filament/Widgets"),
                for: "App\\Filament\\Widgets",
            )
            ->widgets([
                // ServerInfo::class,
                SystemResource::class,
                AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                PelangganOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
