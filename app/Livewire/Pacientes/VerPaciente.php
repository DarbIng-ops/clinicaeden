<?php

namespace App\Livewire\Pacientes;

use Livewire\Component;
use App\Models\Paciente;

class VerPaciente extends Component
{
    public $paciente;

    public function mount($pacienteId)
    {
        $this->paciente = Paciente::with(['historiaClinica', 'citas', 'consultas'])
                                   ->findOrFail($pacienteId);
    }

    public function render()
    {
        return view('livewire.pacientes.ver-paciente');
    }
}