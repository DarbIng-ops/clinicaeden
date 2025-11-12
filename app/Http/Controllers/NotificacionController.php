<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = auth()->user()->notificacionesRecibidas()->paginate(20);
        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida($id)
    {
        $notificacion = auth()->user()->notificacionesRecibidas()->findOrFail($id);
        $notificacion->marcarComoLeida();
        
        return response()->json(['success' => true]);
    }

    public function marcarTodasLeidas()
    {
        auth()->user()->notificacionesRecibidas()->where('leida', false)->update(['leida' => true, 'fecha_leida' => now()]);
        
        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }
}