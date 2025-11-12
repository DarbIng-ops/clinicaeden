<?php

namespace Tests\Feature\Pacientes;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Consulta;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PacienteDerivacionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function recepcionista_puede_derivar_paciente_a_medico()
    {
        // Crear usuarios
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);
        
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        // Crear paciente
        $paciente = Paciente::factory()->create();

        // Autenticarse como recepcionista
        $this->actingAs($recepcionista);

        // Derivar paciente a médico
        $response = $this->post('/recepcion/consultas', [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'motivo_consulta' => 'Dolor de cabeza'
        ]);

        // Verificar que se creó la consulta
        $this->assertDatabaseHas('consultas', [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'estado' => 'pendiente'
        ]);

        // Verificar redirección
        $response->assertRedirect();
    }
}