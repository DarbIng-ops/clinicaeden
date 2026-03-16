<?php

/**
 * HabitacionController.php
 *
 * CRUD de habitaciones: administra las habitaciones de hospitalización por módulo.
 *
 * @package PulsoCore
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\ModuloEnfermeria;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    /**
     * Lista todas las habitaciones ordenadas por módulo y número.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $habitaciones = Habitacion::with(['modulo.piso'])
            ->orderBy('modulo_id')
            ->orderBy('numero')
            ->get();

        return view('habitaciones.index', compact('habitaciones'));
    }

    /**
     * Muestra el detalle de una habitación con sus hospitalizaciones.
     *
     * @param  \App\Models\Habitacion  $habitacion
     * @return \Illuminate\View\View
     */
    public function show(Habitacion $habitacion)
    {
        $habitacion->load([
            'modulo.piso',
            'hospitalizaciones.paciente',
            'hospitalizaciones.medicoGeneral'
        ]);

        return view('habitaciones.show', compact('habitacion'));
    }

    /**
     * Muestra el formulario para crear una nueva habitación.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $modulos = ModuloEnfermeria::activos()
            ->with('piso')
            ->orderBy('piso_id')
            ->orderBy('nombre')
            ->get();

        return view('habitaciones.create', compact('modulos'));
    }

    /**
     * Almacena una nueva habitación validada en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Muestra el formulario de edición de una habitación existente.
     *
     * @param  \App\Models\Habitacion  $habitacion
     * @return \Illuminate\View\View
     */
    public function edit(Habitacion $habitacion)
    {
        $modulos = ModuloEnfermeria::activos()
            ->with('piso')
            ->orderBy('piso_id')
            ->orderBy('nombre')
            ->get();

        return view('habitaciones.edit', compact('habitacion', 'modulos'));
    }

    /**
     * Actualiza los datos de una habitación en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Habitacion    $habitacion
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Elimina una habitación del sistema.
     *
     * @param  \App\Models\Habitacion  $habitacion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Habitacion $habitacion)
    {
        $habitacion->delete();

        return redirect()->route('habitaciones.index')
            ->with('success', 'Habitación eliminada exitosamente.');
    }

    /**
     * Lista las habitaciones disponibles con capacidad libre para nuevas hospitalizaciones.
     *
     * @return \Illuminate\View\View
     */
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
