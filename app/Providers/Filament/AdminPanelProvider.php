<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->profile(\App\Filament\Pages\EditProfile::class, isSimple: false)
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('TrustedU')
            ->brandLogo(asset('assets/images/logo-color.png'))
            ->brandLogoHeight('2.2rem')
            ->favicon(asset('favicon.png'))
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            ->maxContentWidth(\Filament\Support\Enums\Width::Full)
            ->globalSearch(false)
            ->renderHook('panels::head.end', fn () => '
                <link rel="stylesheet" href="' . asset('css/admin-custom.css') . '?v=' . filemtime(public_path('css/admin-custom.css')) . '">
            ')
            ->renderHook('panels::body.end', fn () => '
                <script src="' . asset('js/admin-custom.js') . '?v=' . filemtime(public_path('js/admin-custom.js')) . '"></script>
                <script src="' . asset('js/notification-system.js') . '?v=' . filemtime(public_path('js/notification-system.js')) . '"></script>
            ')
            ->renderHook('panels::body.start', function () {
                if (!session()->has('impersonator_id')) return '';
                $name = session('impersonator_name', 'Super Admin');
                return '
                    <div style="position:fixed;bottom:0;left:0;right:0;z-index:99999;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;padding:8px 20px;display:flex;align-items:center;justify-content:center;gap:12px;font-size:13px;font-weight:600;box-shadow:0 -2px 10px rgba(0,0,0,.15);">
                        <svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        <span>You are impersonating <strong>' . e(auth()->user()?->name ?? '') . '</strong></span>
                        <a href="' . route('impersonation.stop') . '" style="display:inline-flex;align-items:center;gap:4px;padding:4px 14px;background:rgba(255,255,255,.2);color:#fff;border-radius:6px;text-decoration:none;font-size:12px;font-weight:700;border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(4px);transition:background .15s;" onmouseover="this.style.background=\'rgba(255,255,255,.35)\'" onmouseout="this.style.background=\'rgba(255,255,255,.2)\'">
                            <svg style="width:14px;height:14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                            Back to ' . e($name) . '
                        </a>
                    </div>
                ';
            })
            ->renderHook('panels::user-menu.before', fn () =>
                \Illuminate\Support\Facades\Blade::render('<livewire:topbar-actions />')
            )
            ->navigationItems([
                // ── CRM: Deals placeholder ──
                NavigationItem::make('Deals')
                    ->url('#')
                    ->icon('heroicon-o-currency-dollar')
                    ->group('CRM')
                    ->sort(2),

                // ── Website CMS: extra items ──
                NavigationItem::make('Website Dashboard')
                    ->url(fn () => route('filament.admin.pages.dashboard'))
                    ->icon('heroicon-o-computer-desktop')
                    ->group('Website CMS')
                    ->sort(1),

                NavigationItem::make('Visit Website')
                    ->url('/')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->group('Website CMS')
                    ->sort(2),

                // ── Platform: placeholder items ──
                NavigationItem::make('Reports')
                    ->url('#')
                    ->icon('heroicon-o-chart-bar')
                    ->group('Platform')
                    ->sort(1),

                NavigationItem::make('Recycle Bin')
                    ->url('#')
                    ->icon('heroicon-o-trash')
                    ->group('Platform')
                    ->sort(4),
            ])
            ->navigationGroups([
                NavigationGroup::make('CRM')
                    ->collapsible(true),
                NavigationGroup::make('Management')
                    ->collapsible(true),
                NavigationGroup::make('Website CMS')
                    ->collapsible(true)
                    ->collapsed(true),
                NavigationGroup::make('Platform')
                    ->collapsible(true)
                    ->collapsed(true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
