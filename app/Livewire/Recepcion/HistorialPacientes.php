<?php

namespace App\Livewire\Recepcion;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Paciente;

class HistorialPacientes extends Component
{
    use WithPagination;

    public string $buscar = '';

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function reactivar(int $id): void
    {
        $paciente = Paciente::findOrFail($id);

        if ($paciente->estado !== 'egresado') {
            return;
        }

        $paciente->update(['estado' => 'activo']);

        session()->flash('success', "Paciente {$paciente->nombres} {$paciente->apellidos} reactivado correctamente.");
    }

    public function render()
    {
        $pacientes = Paciente::withCount('consultas')
            ->with(['consultas' => fn($q) => $q->latest()->limit(1)])
            ->when($this->buscar, fn($q) => $q->where(function ($q) {
                $q->where('nombres', 'like', "%{$this->buscar}%")
                  ->orWhere('apellidos', 'like', "%{$this->buscar}%")
                  ->orWhere('dni', 'like', "%{$this->buscar}%");
            }))
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.recepcion.historial-pacientes', compact('pacientes'))
            ->extends('layouts.adminlte')
            ->section('content');
    }
}
