<?php

namespace App\Http\Controllers;

use App\Models\Piso;
use App\Models\ModuloEnfermeria;
use App\Models\Consultorio;
use App\Models\Habitacion;
use Illuminate\Http\Request;

class PisoController extends Controller
{
    public function index()
    {
        $pisos = Piso::activos()
            ->with(['consultorios', 'modulosEnfermeria'])
            ->orderBy('numero')
            ->get();

        return view('pisos.index', compact('pisos'));
    }

    public function show(Piso $piso)
    {
        $piso->load([
            'consultorios',
            'modulosEnfermeria.jefeEnfermeria',
            'modulosEnfermeria.auxiliares',
            'modulosEnfermeria.habitaciones',
            'modulosEnfermeria.salasProcedimientos'
        ]);

        return view('pisos.show', compact('piso'));
    }

    public function create()
    {
        return view('pisos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|integer|unique:pisos,numero',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Piso::create($request->all());

        return redirect()->route('pisos.index')
            ->with('success', 'Piso creado exitosamente.');
    }

    public function edit(Piso $piso)
    {
        return view('pisos.edit', compact('piso'));
    }

    public function update(Request $request, Piso $piso)
    {
        $request->validate([
            'numero' => 'required|integer|unique:pisos,numero,' . $piso->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $piso->update($request->all());

        return redirect()->route('pisos.index')
            ->with('success', 'Piso actualizado exitosamente.');
    }

    public function destroy(Piso $piso)
    {
        $piso->delete();

        return redirect()->route('pisos.index')
            ->with('success', 'Piso eliminado exitosamente.');
    }
}
