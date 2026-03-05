<?php

namespace App\Listeners;

use App\Models\AuditoriaAcceso;
use Illuminate\Auth\Events\Logout;

/**
 * RegistrarLogout
 *
 * Registra el evento de cierre de sesión conforme a ISO 27001.
 */
class RegistrarLogout
{
    public function handle(Logout $event): void
    {
        AuditoriaAcceso::registrar('logout');
    }
}
