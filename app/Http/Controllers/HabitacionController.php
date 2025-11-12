<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\ModuloEnfermeria;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    public function index()
    {
        $habitaciones = Habitacion::with(['modulo.piso'])
            ->orderBy('modulo_id')
            ->orderBy('numero')
            ->get();

        return view('habitaciones.index', compact('habitaciones'));
    }

    public function show(Habitacion $habitacion)
    {
        $habitacion->load([
            'modulo.piso',
            'hospitalizaciones.paciente',
            'hospitalizaciones.medicoGeneral'
        ]);

        return view('habitaciones.show', compact('habitacion'));
    }

    public function create()
    {
        $modulos = ModuloEnfermeria::activos()
            ->with('piso')
            ->orderBy('piso_id')
            ->orderBy('nombre')
            ->get();

        return view('habitaciones.create', compact('modulos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:10|unique:habitaciones,numero',
            'modulo_id' => 'required|exists:modulos_enfermeria,id',
            'capacidad' => 'required|integer|min:1|max:10',
            'tipo' => 'required|in:general,partos,neonatos,madres_pre_post_parto',
            'descripcion' => 'nullable|string',
        ]);

        Habitacion::create($request->all());

        return redirect()->route('habitaciones.index')
            ->with('success', 'Habitación creada exitosamente.');
    }

    public function edit(Habitacion $habitacion)
    {
        $modulos = ModuloEnfermeria::activos()
            ->with('piso')
            ->orderBy('piso_id')
            ->orderBy('nombre')
            ->get();

        return view('habitaciones.edit', compact('habitacion', 'modulos'));
    }

    public function update(Request $request, Habitacion $habitacion)
    {
        $request->validate([
            'numero' => 'required|string|max:10|unique:habitaciones,numero,' . $habitacion->id,
            'modulo_id' => 'required|exists:modulos_enfermeria,id',
            'capacidad' => 'required|integer|min:1|max:10',
            'tipo' => 'required|in:general,partos,neonatos,madres_pre_post_parto',
            'descripcion' => 'nullable|string',
        ]);

        $habitacion->update($request->all());

        return redirect()->route('habitaciones.index')
            ->with('success', 'Habitación actualizada exitosamente.');
    }

    public function destroy(Habitacion $habitacion)
    {
        $habitacion->delete();

        return redirect()->route('habitaciones.index')
            ->with('success', 'Habitación eliminada exitosamente.');
    }

    public function disponibles()
    {
        $habitaciones = Habitacion::disponibles()
            ->conCapacidadDisponible()
            ->with(['modulo.piso'])
            ->orderBy('modulo_id')
            ->orderBy('numero')
            ->get();

        return view('habitaciones.disponibles', compact('habitaciones'));
    }
}
