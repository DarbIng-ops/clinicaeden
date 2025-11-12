<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    /**
     * Redirigir al usuario autenticado hacia el dashboard correspondiente.
     *
     * @return \Illuminate\Http\RedirectResponse Redirección basada en el rol del usuario
     */
    public function __invoke()
    {
        $user = auth()->user();

        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'recepcionista' => redirect()->route('recepcion.dashboard'),
            'medico_general' => redirect()->route('medico_general.dashboard'),
            'medico_especialista' => redirect()->route('medico_especialista.dashboard'),
            'jefe_enfermeria' => redirect()->route('jefe-enfermeria.dashboard'),
            'auxiliar_enfermeria' => redirect()->route('auxiliar-enfermeria.dashboard'),
            'caja' => redirect()->route('caja.dashboard'),
            default => redirect('/'),
        };
    }
}