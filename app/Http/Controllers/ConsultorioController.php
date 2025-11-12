<?php

namespace App\Http\Controllers;

use App\Models\Consultorio;
use App\Models\Piso;
use Illuminate\Http\Request;

class ConsultorioController extends Controller
{
    public function index()
    {
        $consultorios = Consultorio::with('piso')
            ->orderBy('piso_id')
            ->orderBy('numero')
            ->get();

        return view('consultorios.index', compact('consultorios'));
    }

    public function show(Consultorio $consultorio)
    {
        $consultorio->load(['piso', 'citas.medico', 'citas.paciente']);

        return view('consultorios.show', compact('consultorio'));
    }

    public function create()
    {
        $pisos = Piso::activos()->orderBy('numero')->get();

        return view('consultorios.create', compact('pisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:10|unique:consultorios,numero',
            'piso_id' => 'required|exists:pisos,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Consultorio::create($request->all());

        return redirect()->route('consultorios.index')
            ->with('success', 'Consultorio creado exitosamente.');
    }

    public function edit(Consultorio $consultorio)
    {
        $pisos = Piso::activos()->orderBy('numero')->get();

        return view('consultorios.edit', compact('consultorio', 'pisos'));
    }

    public function update(Request $request, Consultorio $consultorio)
    {
        $request->validate([
            'numero' => 'required|string|max:10|unique:consultorios,numero,' . $consultorio->id,
            'piso_id' => 'required|exists:pisos,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $consultorio->update($request->all());

        return redirect()->route('consultorios.index')
            ->with('success', 'Consultorio actualizado exitosamente.');
    }

    public function destroy(Consultorio $consultorio)
    {
        $consultorio->delete();

        return redirect()->route('consultorios.index')
            ->with('success', 'Consultorio eliminado exitosamente.');
    }
}
