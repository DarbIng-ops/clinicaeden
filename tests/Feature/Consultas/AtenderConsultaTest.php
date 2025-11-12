<?php

namespace Tests\Feature\Consultas;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\HistoriaClinica;
use App\Models\Factura;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CP-006: Atender consulta médica
 * 
 * Verificar que el médico puede atender una consulta y registrar un diagnóstico.
 */
class AtenderConsultaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function medico_puede_atender_consulta_y_registrar_diagnostico()
    {
        // Precondiciones: Consulta registrada, paciente asignado al médico
        
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        // Crear paciente con historia clínica
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta pendiente
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Dolor de cabeza',
            'motivo_consulta' => 'Dolor de cabeza',
            'estado' => 'pendiente'
        ]);

        // Autenticarse como médico
        $this->actingAs($medico);

        // Paso 2: Registrar signos vitales y diagnóstico
        $response = $this->post("/medico-general/consultas/{$consulta->id}/finalizar", [
            'presion_arterial' => '120/80',
            'temperatura' => 36.5,
            'frecuencia_cardiaca' => 72,
            'frecuencia_respiratoria' => 16,
            'saturacion_oxigeno' => 98,
            'peso' => 70.5,
            'talla' => 1.75,
            'diagnostico' => 'Cefalea tensional',
            'tratamiento' => 'Reposo y analgésicos',
            'accion' => 'caja'
        ]);

        // Verificar que la consulta se completó
        $this->assertDatabaseHas('consultas', [
            'id' => $consulta->id,
            'estado' => 'completada',
            'diagnostico' => 'Cefalea tensional',
            'presion_arterial' => '120/80',
            'temperatura' => 36.5
        ]);

        // Verificar que la consulta completada no aparece en pendientes
        $response->assertRedirect();
        
        // Verificar que la consulta no está en pendientes
        $consultasPendientes = Consulta::where('medico_id', $medico->id)
            ->where('estado', 'pendiente')
            ->get();
        
        $this->assertFalse($consultasPendientes->contains('id', $consulta->id));
    }

    /** @test */
    public function medico_no_puede_atender_consulta_de_otro_medico()
    {
        // Crear dos médicos
        $medico1 = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        $medico2 = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        // Crear paciente con historia clínica
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta del médico 1
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico1->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Dolor de cabeza',
            'motivo_consulta' => 'Dolor de cabeza',
            'estado' => 'pendiente'
        ]);

        // Autenticarse como médico 2
        $this->actingAs($medico2);

        // Intentar atender la consulta del médico 1
        $response = $this->post("/medico-general/consultas/{$consulta->id}/finalizar", [
            'diagnostico' => 'Cefalea tensional',
            'tratamiento' => 'Reposo',
            'accion' => 'caja'
        ]);

        // Verificar que se denegó el acceso
        $response->assertStatus(403);
    }
}

