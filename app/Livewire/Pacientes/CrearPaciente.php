<?php

namespace App\Livewire\Pacientes;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Paciente;
use App\Models\HistoriaClinica;

class CrearPaciente extends Component
{
    use WithFileUploads;

    public $dni;
    public $nombres;
    public $apellidos;
    public $fecha_nacimiento;
    public $sexo = 'M';
    public $telefono;
    public $email;
    public $direccion;
    public $ciudad = 'Montevideo';
    public $tipo_sangre;
    public $alergias;
    public $enfermedades_cronicas;
    public $contacto_emergencia_nombre;
    public $contacto_emergencia_telefono;
    public $foto;

    protected $rules = [
        'dni' => 'required|unique:pacientes,dni|max:20',
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'fecha_nacimiento' => 'required|date|before:today',
        'sexo' => 'required|in:M,F,Otro',
        'telefono' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'direccion' => 'required|string',
        'ciudad' => 'required|string|max:255',
        'tipo_sangre' => 'nullable|string|max:10',
        'alergias' => 'nullable|string',
        'enfermedades_cronicas' => 'nullable|string',
        'contacto_emergencia_nombre' => 'required|string|max:255',
        'contacto_emergencia_telefono' => 'required|string|max:20',
        'foto' => 'nullable|image|max:2048',
    ];

    public function guardar()
    {
        $this->validate();

        try {
            $fotoPath = null;
            if ($this->foto) {
                $fotoPath = $this->foto->store('pacientes', 'public');
            }

            $paciente = Paciente::create([
                'dni' => $this->dni,
                'nombres' => $this->nombres,
                'apellidos' => $this->apellidos,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'sexo' => $this->sexo,
                'telefono' => $this->telefono,
                'email' => $this->email,
                'direccion' => $this->direccion,
                'ciudad' => $this->ciudad,
                'tipo_sangre' => $this->tipo_sangre,
                'alergias' => $this->alergias,
                'enfermedades_cronicas' => $this->enfermedades_cronicas,
                'contacto_emergencia_nombre' => $this->contacto_emergencia_nombre,
                'contacto_emergencia_telefono' => $this->contacto_emergencia_telefono,
                'foto' => $fotoPath,
                'activo' => true,
            ]);

            HistoriaClinica::create(['paciente_id' => $paciente->id]);

            session()->flash('success', 'Paciente registrado exitosamente.');
            
            return redirect()->route('recepcion.pacientes.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pacientes.crear-paciente');
    }
}