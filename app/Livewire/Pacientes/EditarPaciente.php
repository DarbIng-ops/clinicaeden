<?php

namespace App\Livewire\Pacientes;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Paciente;
use Illuminate\Support\Facades\Storage;

class EditarPaciente extends Component
{
    use WithFileUploads;

    public $paciente;
    public $dni;
    public $nombres;
    public $apellidos;
    public $fecha_nacimiento;
    public $sexo;
    public $telefono;
    public $email;
    public $direccion;
    public $ciudad;
    public $tipo_sangre;
    public $alergias;
    public $enfermedades_cronicas;
    public $contacto_emergencia_nombre;
    public $contacto_emergencia_telefono;
    public $foto;
    public $foto_actual;

    public function mount($pacienteId)
    {
        $this->paciente = Paciente::findOrFail($pacienteId);
        
        // Cargar datos existentes
        $this->dni = $this->paciente->dni;
        $this->nombres = $this->paciente->nombres;
        $this->apellidos = $this->paciente->apellidos;
        $this->fecha_nacimiento = $this->paciente->fecha_nacimiento->format('Y-m-d');
        $this->sexo = $this->paciente->sexo;
        $this->telefono = $this->paciente->telefono;
        $this->email = $this->paciente->email;
        $this->direccion = $this->paciente->direccion;
        $this->ciudad = $this->paciente->ciudad;
        $this->tipo_sangre = $this->paciente->tipo_sangre;
        $this->alergias = $this->paciente->alergias;
        $this->enfermedades_cronicas = $this->paciente->enfermedades_cronicas;
        $this->contacto_emergencia_nombre = $this->paciente->contacto_emergencia_nombre;
        $this->contacto_emergencia_telefono = $this->paciente->contacto_emergencia_telefono;
        $this->foto_actual = $this->paciente->foto;
    }

    protected function rules()
    {
        return [
            'dni' => 'required|max:20|unique:pacientes,dni,' . $this->paciente->id,
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
    }

    public function actualizar()
    {
        $this->validate();

        try {
            $fotoPath = $this->foto_actual;

            // Si hay nueva foto, guardarla y eliminar la anterior
            if ($this->foto) {
                if ($this->foto_actual) {
                    Storage::disk('public')->delete($this->foto_actual);
                }
                $fotoPath = $this->foto->store('pacientes', 'public');
            }

            $this->paciente->update([
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
            ]);

            session()->flash('success', 'Paciente actualizado exitosamente.');
            
            return redirect()->route('recepcion.pacientes.ver', $this->paciente->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pacientes.editar-paciente');
    }
}