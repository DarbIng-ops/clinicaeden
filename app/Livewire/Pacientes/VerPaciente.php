<?php

/**
 * VerPaciente.php
 *
 * Componente Livewire para mostrar el perfil completo de un paciente.
 *
 * @package PulsoCore
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Livewire\Pacientes;

use Livewire\Component;
use App\Models\Paciente;

class VerPaciente extends Component
{
    /** @var \App\Models\Paciente $paciente Instancia del paciente cargado */
    public $paciente;

    /**
     * Carga el paciente con sus relaciones al inicializar el componente.
     *
     * @param  int  $pacienteId
     * @return void
     */
    public function mount($pacienteId)
    {
        $this->paciente = Paciente::with(['historiaClinica', 'citas', 'consultas'])
                                   ->findOrFail($pacienteId);
    }

    /**
     * Renderiza la vista del perfil del paciente.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.pacientes.ver-paciente');
    }
}