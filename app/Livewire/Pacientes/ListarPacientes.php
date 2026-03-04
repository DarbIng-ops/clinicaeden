<?php

/**
 * ListarPacientes.php
 *
 * Componente Livewire con búsqueda y paginación reactiva para listar pacientes.
 *
 * @package ClinicaEden
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Livewire\Pacientes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Paciente;

class ListarPacientes extends Component
{
    use WithPagination;

    /** @var string $search Término para filtrar pacientes por nombre, apellido o DNI */
    public $search = '';

    /** @var int $perPage Número de registros por página */
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    /**
     * Reinicia la paginación cada vez que cambia el término de búsqueda.
     *
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Renderiza el componente con la lista filtrada y paginada de pacientes activos.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $pacientes = Paciente::query()
            ->when($this->search, function($query) {
                $query->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
            })
            ->where('activo', true)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.pacientes.listar-pacientes', [
            'pacientes' => $pacientes
        ]);
    }
}