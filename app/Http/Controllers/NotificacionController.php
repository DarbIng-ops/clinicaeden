<?php

/**
 * NotificacionController.php
 *
 * Gestiona las notificaciones del sistema: listado, marcar leídas y estado del buzón.
 *
 * @package PulsoCore
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Lista todas las notificaciones del usuario autenticado con paginación.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notificaciones = auth()->user()->notificacionesRecibidas()->paginate(20);
        return view('notificaciones.index', compact('notificaciones'));
    }

    /**
     * Marca una notificación específica como leída.
     * Responde con JSON para peticiones AJAX o redirige para peticiones web.
     *
     * @param  int|string  $id  ID de la notificación
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function marcarLeida($id)
    {
        $notificacion = auth()->user()->notificacionesRecibidas()->findOrFail($id);
        $notificacion->marcarComoLeida();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notificación marcada como leída.');
    }

    /**
     * Marca todas las notificaciones no leídas del usuario como leídas.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function marcarTodasLeidas()
    {
        auth()->user()->notificacionesRecibidas()->where('leida', false)->update(['leida' => true, 'fecha_leida' => now()]);

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }
}