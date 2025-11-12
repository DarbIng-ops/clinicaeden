<?php

namespace App\Http\Controllers;

use App\Models\ModuloEnfermeria;
use App\Models\Piso;
use App\Models\User;
use Illuminate\Http\Request;

class ModuloEnfermeriaController extends Controller
{
    public function index()
    {
        $modulos = ModuloEnfermeria::activos()
            ->with(['piso', 'jefeEnfermeria', 'auxiliares', 'habitaciones', 'salasProcedimientos'])
            ->orderBy('piso_id')
            ->orderBy('nombre')
            ->get();

        return view('modulos.index', compact('modulos'));
    }

    public function show(ModuloEnfermeria $modulo)
    {
        $modulo->load([
            'piso',
            'jefeEnfermeria',
            'auxiliares',
            'habitaciones',
            'salasProcedimientos'
        ]);

        return view('modulos.show', compact('modulo'));
    }

    public function create()
    {
        $pisos = Piso::activos()->orderBy('numero')->get();
        $jefesEnfermeria = User::where('role', 'jefe_enfermeria')
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('modulos.create', compact('pisos', 'jefesEnfermeria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'piso_id' => 'required|exists:pisos,id',
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:general,partos_neonatos,hospitalizacion_general',
            'descripcion' => 'nullable|string',
            'jefe_enfermeria_id' => 'nullable|exists:users,id',
        ]);

        ModuloEnfermeria::create($request->all());

        return redirect()->route('modulos.index')
            ->with('success', 'Módulo de enfermería creado exitosamente.');
    }

    public function edit(ModuloEnfermeria $modulo)
    {
        $pisos = Piso::activos()->orderBy('numero')->get();
        $jefesEnfermeria = User::where('role', 'jefe_enfermeria')
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('modulos.edit', compact('modulo', 'pisos', 'jefesEnfermeria'));
    }

    public function update(Request $request, ModuloEnfermeria $modulo)
    {
        $request->validate([
            'piso_id' => 'required|exists:pisos,id',
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:general,partos_neonatos,hospitalizacion_general',
            'descripcion' => 'nullable|string',
            'jefe_enfermeria_id' => 'nullable|exists:users,id',
        ]);

        $modulo->update($request->all());

        return redirect()->route('modulos.index')
            ->with('success', 'Módulo de enfermería actualizado exitosamente.');
    }

    public function destroy(ModuloEnfermeria $modulo)
    {
        $modulo->delete();

        return redirect()->route('modulos.index')
            ->with('success', 'Módulo de enfermería eliminado exitosamente.');
    }

    public function asignarAuxiliar(Request $request, ModuloEnfermeria $modulo)
    {
        $request->validate([
            'auxiliar_enfermeria_id' => 'required|exists:users,id',
        ]);

        $modulo->auxiliares()->attach($request->auxiliar_enfermeria_id, ['activo' => true]);

        return redirect()->route('modulos.show', $modulo)
            ->with('success', 'Auxiliar asignado exitosamente.');
    }

    public function desasignarAuxiliar(ModuloEnfermeria $modulo, User $auxiliar)
    {
        $modulo->auxiliares()->updateExistingPivot($auxiliar->id, ['activo' => false]);

        return redirect()->route('modulos.show', $modulo)
            ->with('success', 'Auxiliar desasignado exitosamente.');
    }
}
