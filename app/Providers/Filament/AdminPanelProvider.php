<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\SiteOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
            ->passwordReset()
            ->profile(isSimple: false)
            ->brandName('Valores Real Estate')
            ->brandLogo(fn () => asset('assets/img/general/logo.png'))
            ->brandLogoHeight('2rem')
            ->favicon(fn () => asset('assets/img/general/favicon.png'))
            ->colors([
                'primary' => Color::hex('#2F6880'),
                'warning' => Color::hex('#F5A800'),
            ])
            ->font('Montserrat')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Contenido'),
                NavigationGroup::make('Páginas'),
                NavigationGroup::make('Formularios'),
                NavigationGroup::make('Configuración'),
            ])
            ->navigationItems([
                NavigationItem::make('Ver sitio')->url(fn () => route('home'), shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)->sort(100),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])
            ->widgets([AccountWidget::class, SiteOverview::class])
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
