<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    /**
     * Listar consultas médicas registradas.
     *
     * Controlador reservado para futuras implementaciones de gestión general de
     * consultas fuera de los módulos específicos de cada rol.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Mostrar formulario para crear una nueva consulta.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Persistir una nueva consulta médica.
     *
     * @param \Illuminate\Http\Request $request Datos de la nueva consulta
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Mostrar los datos de una consulta específica.
     *
     * @param string $id Identificador de la consulta
     * @return void
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Mostrar formulario de edición de una consulta.
     *
     * @param string $id Identificador de la consulta a editar
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualizar la información de una consulta.
     *
     * @param \Illuminate\Http\Request $request Datos actualizados
     * @param string $id Identificador de la consulta
     * @return void
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Eliminar una consulta del sistema.
     *
     * @param string $id Identificador de la consulta a eliminar
     * @return void
     */
    public function destroy(string $id)
    {
        //
    }
}
