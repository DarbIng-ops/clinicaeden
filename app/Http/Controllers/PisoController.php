<?php

/**
 * PisoController.php
 *
 * CRUD de pisos del edificio hospitalario: creación, edición y eliminación.
 *
 * @package ClinicaEden
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Http\Controllers;

use App\Models\Piso;
use App\Models\ModuloEnfermeria;
use App\Models\Consultorio;
use App\Models\Habitacion;
use Illuminate\Http\Request;

class PisoController extends Controller
{
    /**
     * Lista todos los pisos activos con sus consultorios y módulos de enfermería.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pisos = Piso::activos()
            ->with(['consultorios', 'modulosEnfermeria'])
            ->orderBy('numero')
            ->get();

        return view('pisos.index', compact('pisos'));
    }

    /**
     * Muestra el detalle completo de un piso con su estructura hospitalaria.
     *
     * @param  \App\Models\Piso  $piso
     * @return \Illuminate\View\View
     */
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

    /**
     * Muestra el formulario para crear un nuevo piso.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('pisos.create');
    }

    /**
     * Almacena un nuevo piso validado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Muestra el formulario de edición de un piso existente.
     *
     * @param  \App\Models\Piso  $piso
     * @return \Illuminate\View\View
     */
    public function edit(Piso $piso)
    {
        return view('pisos.edit', compact('piso'));
    }

    /**
     * Actualiza los datos de un piso en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Piso          $piso
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Elimina un piso del sistema.
     *
     * @param  \App\Models\Piso  $piso
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Piso $piso)
    {
        $piso->delete();

        return redirect()->route('pisos.index')
            ->with('success', 'Piso eliminado exitosamente.');
    }
}
