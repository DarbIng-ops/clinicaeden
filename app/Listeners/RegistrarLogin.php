<?php

namespace App\Listeners;

use App\Models\AuditoriaAcceso;
use Illuminate\Auth\Events\Login;

/**
 * RegistrarLogin
 *
 * Registra el evento de inicio de sesión conforme a ISO 27001.
 */
class RegistrarLogin
{
    public function handle(Login $event): void
    {
        AuditoriaAcceso::registrar('login');
    }
}
