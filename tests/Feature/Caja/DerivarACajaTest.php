<?php

namespace Tests\Feature\Caja;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\HistoriaClinica;
use App\Models\Factura;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CP-007: Derivar a caja
 * 
 * Verificar flujo de derivación a caja después de una consulta.
 */
class DerivarACajaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function derivar_consulta_a_caja_genera_factura_pendiente()
    {
        // Precondiciones: Consulta finalizada
        
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

        // Crear usuario de caja para notificaciones
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        // Autenticarse como médico
        $this->actingAs($medico);

        // Paso 1: Finalizar consulta
        // Paso 2: Seleccionar "Derivar a Caja"
        // Paso 3: Confirmar
        $response = $this->post("/medico-general/consultas/{$consulta->id}/finalizar", [
            'diagnostico' => 'Cefalea tensional',
            'tratamiento' => 'Reposo y analgésicos',
            'accion' => 'caja'
        ]);

        // Resultado Esperado: 
        // - Factura generada con estado "pendiente"
        // - Caja notificada
        // - Paciente en lista de cobros
        
        // Verificar que se creó la factura con estado pendiente
        $this->assertDatabaseHas('facturas', [
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'estado' => 'pendiente',
            'metodo_pago' => 'efectivo',
            'total' => 1000
        ]);

        // Verificar que se notificó a caja
        $this->assertDatabaseHas('notificaciones_sistema', [
            'usuario_receptor_id' => $caja->id,
            'tipo' => 'derivacion_caja',
            'leida' => false
        ]);

        // Verificar que la factura generada tiene estado "pendiente"
        $factura = Factura::where('consulta_id', $consulta->id)->first();
        $this->assertNotNull($factura);
        $this->assertEquals('pendiente', $factura->estado);
        $this->assertEquals('efectivo', $factura->metodo_pago);
        
        // Verificar redirección
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function caja_recibe_notificacion_de_derivacion()
    {
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        // Crear paciente con historia clínica
        $paciente = Paciente::factory()->create([
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
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

        // Crear usuario de caja
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        // Autenticarse como médico
        $this->actingAs($medico);

        // Derivar a caja
        $response = $this->post("/medico-general/consultas/{$consulta->id}/finalizar", [
            'diagnostico' => 'Cefalea tensional',
            'tratamiento' => 'Reposo',
            'accion' => 'caja'
        ]);

        // Verificar que caja recibió notificación con información correcta
        $notificacion = \App\Models\NotificacionSistema::where('usuario_receptor_id', $caja->id)
            ->where('tipo', 'derivacion_caja')
            ->first();
            
        $this->assertNotNull($notificacion);
        $this->assertEquals('Paciente derivado a Caja', $notificacion->titulo);
        $this->assertStringContainsString('Juan Pérez', $notificacion->mensaje);
        $this->assertStringContainsString('$1000', $notificacion->mensaje);
        $this->assertFalse($notificacion->leida);
    }
}

