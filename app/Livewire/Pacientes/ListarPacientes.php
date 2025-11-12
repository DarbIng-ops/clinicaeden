<?php

namespace App\Livewire\Pacientes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Paciente;

class ListarPacientes extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

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