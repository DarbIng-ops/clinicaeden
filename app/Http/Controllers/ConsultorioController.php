<?php

/**
 * ConsultorioController.php
 *
 * CRUD de consultorios: administra los consultorios físicos del centro médico.
 *
 * @package ClinicaEden
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Http\Controllers;

use App\Models\Consultorio;
use App\Models\Piso;
use Illuminate\Http\Request;

class ConsultorioController extends Controller
{
    /**
     * Lista todos los consultorios ordenados por piso y número.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $consultorios = Consultorio::with('piso')
            ->orderBy('piso_id')
            ->orderBy('numero')
            ->get();

        return view('consultorios.index', compact('consultorios'));
    }

    /**
     * Muestra el detalle de un consultorio con sus citas asociadas.
     *
     * @param  \App\Models\Consultorio  $consultorio
     * @return \Illuminate\View\View
     */
    public function show(Consultorio $consultorio)
    {
        $consultorio->load(['piso', 'citas.medico', 'citas.paciente']);

        return view('consultorios.show', compact('consultorio'));
    }

    /**
     * Muestra el formulario para crear un nuevo consultorio.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $pisos = Piso::activos()->orderBy('numero')->get();

        return view('consultorios.create', compact('pisos'));
    }

    /**
     * Almacena un nuevo consultorio validado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Muestra el formulario de edición de un consultorio existente.
     *
     * @param  \App\Models\Consultorio  $consultorio
     * @return \Illuminate\View\View
     */
    public function edit(Consultorio $consultorio)
    {
        $pisos = Piso::activos()->orderBy('numero')->get();

        return view('consultorios.edit', compact('consultorio', 'pisos'));
    }

    /**
     * Actualiza los datos de un consultorio en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consultorio   $consultorio
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Elimina un consultorio del sistema.
     *
     * @param  \App\Models\Consultorio  $consultorio
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Consultorio $consultorio)
    {
        $consultorio->delete();

        return redirect()->route('consultorios.index')
            ->with('success', 'Consultorio eliminado exitosamente.');
    }
}
