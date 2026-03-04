<?php

/**
 * ModuloEnfermeriaController.php
 *
 * CRUD de módulos de enfermería: gestiona módulos, asignación de jefes y auxiliares.
 *
 * @package ClinicaEden
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Http\Controllers;

use App\Models\ModuloEnfermeria;
use App\Models\Piso;
use App\Models\User;
use Illuminate\Http\Request;

class ModuloEnfermeriaController extends Controller
{
    /**
     * Lista todos los módulos de enfermería activos con su estructura completa.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $modulos = ModuloEnfermeria::activos()
            ->with(['piso', 'jefeEnfermeria', 'auxiliares', 'habitaciones', 'salasProcedimientos'])
            ->orderBy('piso_id')
            ->orderBy('nombre')
            ->get();

        return view('modulos.index', compact('modulos'));
    }

    /**
     * Muestra el detalle de un módulo con jefe, auxiliares, habitaciones y salas.
     *
     * @param  \App\Models\ModuloEnfermeria  $modulo
     * @return \Illuminate\View\View
     */
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

    /**
     * Muestra el formulario para crear un nuevo módulo de enfermería.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $pisos = Piso::activos()->orderBy('numero')->get();
        $jefesEnfermeria = User::where('role', 'jefe_enfermeria')
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('modulos.create', compact('pisos', 'jefesEnfermeria'));
    }

    /**
     * Almacena un nuevo módulo de enfermería validado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Muestra el formulario de edición de un módulo de enfermería existente.
     *
     * @param  \App\Models\ModuloEnfermeria  $modulo
     * @return \Illuminate\View\View
     */
    public function edit(ModuloEnfermeria $modulo)
    {
        $pisos = Piso::activos()->orderBy('numero')->get();
        $jefesEnfermeria = User::where('role', 'jefe_enfermeria')
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('modulos.edit', compact('modulo', 'pisos', 'jefesEnfermeria'));
    }

    /**
     * Actualiza los datos de un módulo de enfermería en la base de datos.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  \App\Models\ModuloEnfermeria  $modulo
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Elimina un módulo de enfermería del sistema.
     *
     * @param  \App\Models\ModuloEnfermeria  $modulo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ModuloEnfermeria $modulo)
    {
        $modulo->delete();

        return redirect()->route('modulos.index')
            ->with('success', 'Módulo de enfermería eliminado exitosamente.');
    }

    /**
     * Asigna un auxiliar de enfermería a un módulo mediante tabla pivote.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  \App\Models\ModuloEnfermeria  $modulo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarAuxiliar(Request $request, ModuloEnfermeria $modulo)
    {
        $request->validate([
            'auxiliar_enfermeria_id' => 'required|exists:users,id',
        ]);

        $modulo->auxiliares()->attach($request->auxiliar_enfermeria_id, ['activo' => true]);

        return redirect()->route('modulos.show', $modulo)
            ->with('success', 'Auxiliar asignado exitosamente.');
    }

    /**
     * Desactiva la asignación de un auxiliar a un módulo (soft-remove en pivot).
     *
     * @param  \App\Models\ModuloEnfermeria  $modulo
     * @param  \App\Models\User              $auxiliar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function desasignarAuxiliar(ModuloEnfermeria $modulo, User $auxiliar)
    {
        $modulo->auxiliares()->updateExistingPivot($auxiliar->id, ['activo' => false]);

        return redirect()->route('modulos.show', $modulo)
            ->with('success', 'Auxiliar desasignado exitosamente.');
    }
}
