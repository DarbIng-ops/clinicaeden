<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Consulta;
use App\Models\Factura;
use App\Models\Paciente;
use App\Observers\AuditoriaObserver;
use App\Listeners\RegistrarLogin;
use App\Listeners\RegistrarLogout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Observers ISO 27001 — trazabilidad de modelos críticos ──
        Paciente::observe(AuditoriaObserver::class);
        Consulta::observe(AuditoriaObserver::class);
        Factura::observe(AuditoriaObserver::class);

        // ── Listeners de autenticación ISO 27001 ────────────────────
        Event::listen(Login::class, RegistrarLogin::class);
        Event::listen(Logout::class, RegistrarLogout::class);
    }
}
