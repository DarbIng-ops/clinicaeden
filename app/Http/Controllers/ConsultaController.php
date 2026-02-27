<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use Illuminate\Support\Facades\Auth;

class ConsultaController extends Controller
{
    /**
     * Listar consultas del médico autenticado.
     * Usado por medico_general y medico_especialista vía resource route.
     */
    public function index()
    {
        $user = Auth::user();

        $consultas = Consulta::with(['paciente'])
            ->where('medico_id', $user->id)
            ->orderBy('fecha_consulta', 'desc')
            ->paginate(15);

        // Determinar vista según rol
        $vista = match($user->role) {
            'medico_general'      => 'medico_general.consultas.index',
            'medico_especialista' => 'medico_especialista.consultas.index',
            default               => 'medico_general.consultas.index',
        };

        return view($vista, compact('consultas'));
    }

    /**
     * Mostrar detalle de una consulta.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $consulta = Consulta::with(['paciente', 'medico', 'historiaClinica'])
            ->where('medico_id', $user->id)
            ->findOrFail($id);

        $vista = match($user->role) {
            'medico_general'      => 'medico_general.consultas.show',
            'medico_especialista' => 'medico_especialista.consultas.show',
            default               => 'medico_general.consultas.show',
        };

        return view($vista, compact('consulta'));
    }

    public function create()
    {
        return redirect()->back()->with('info', 'Use el flujo de paciente para crear consultas.');
    }

    public function store(Request $request)
    {
        return redirect()->back()->with('info', 'Use el flujo de paciente para crear consultas.');
    }

    public function edit(string $id)
    {
        return redirect()->back()->with('info', 'Edición no disponible.');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->back()->with('info', 'Edición no disponible.');
    }

    public function destroy(string $id)
    {
        return redirect()->back()->with('info', 'Eliminación no disponible.');
    }
}
