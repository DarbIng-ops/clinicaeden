<?php

namespace Tests\Feature\Recepcion;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\HistoriaClinica;
use App\Models\Factura;
use App\Models\EncuestaSatisfaccion;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CP-009: Procesar salida paciente
 * 
 * Verificar flujo completo de salida con encuesta.
 */
class ProcesarSalidaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function recepcionista_puede_procesar_salida_completa_con_encuesta()
    {
        // Precondiciones: Paciente con consulta completada y pago realizado
        
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);
        
        // Crear recepcionista
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);
        
        // Crear paciente
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta completada
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Dolor de cabeza',
            'motivo_consulta' => 'Dolor de cabeza',
            'estado' => 'completada'
        ]);

        // Crear factura pagada
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000001',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pagado',
            'fecha_emision' => now(),
            'fecha_pago' => now()
        ]);

        // Autenticarse como recepcionista
        $this->actingAs($recepcionista);

        // Paso 1: Ver lista de salidas
        // Paso 2: Clic en Dar Salida (procesarSalida)
        // Paso 3: Completar encuesta
        // Paso 4: Confirmar
        $response = $this->post("/recepcion/pacientes/{$paciente->id}/confirmar-salida", [
            'atencion_medica' => 5,
            'tiempo_espera' => 4,
            'trato_personal' => 5,
            'comentarios_encuesta' => 'Excelente atención',
            'observaciones_salida' => 'Paciente en buen estado'
        ]);

        // Resultado Esperado: 
        // - Encuesta guardada
        // - Paciente desaparece de listas
        
        // Verificar que se guardó la encuesta
        $this->assertDatabaseHas('encuestas_satisfaccion', [
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'recepcion_id' => $recepcionista->id,
            'atencion_medica' => 5,
            'personal_recepcion' => 5,
            'tiempo_espera' => 4,
            'calidad_general' => round((5 + 5 + 4) / 3, 1)
        ]);

        // Verificar que la encuesta tiene los datos correctos
        $encuesta = EncuestaSatisfaccion::where('paciente_id', $paciente->id)->first();
        $this->assertNotNull($encuesta);
        $this->assertEquals(5, $encuesta->atencion_medica);
        $this->assertEquals(4, $encuesta->tiempo_espera);
        $this->assertEquals(5, $encuesta->personal_recepcion);
        $this->assertStringContainsString('Excelente atención', $encuesta->comentarios);
        
        // Verificar redirección
        $response->assertRedirect(route('recepcion.salidas'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function paciente_desaparece_de_listas_despues_de_salida()
    {
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);
        
        // Crear recepcionista
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);
        
        // Crear paciente
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta completada
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Control',
            'motivo_consulta' => 'Control',
            'estado' => 'completada'
        ]);

        // Crear factura pagada
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000002',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pagado',
            'fecha_emision' => now(),
            'fecha_pago' => now()
        ]);

        // Autenticarse como recepcionista
        $this->actingAs($recepcionista);

        // Verificar que el paciente está en lista de salidas antes
        $pacientesListosAntes = Factura::where('estado', 'pagado')
            ->whereHas('consulta', function($q) {
                $q->where('estado', 'completada');
            })
            ->whereDoesntHave('consulta.encuestaSatisfaccion')
            ->get();
        
        $this->assertTrue($pacientesListosAntes->contains('consulta_id', $consulta->id));

        // Procesar salida
        $this->post("/recepcion/pacientes/{$paciente->id}/confirmar-salida", [
            'atencion_medica' => 4,
            'tiempo_espera' => 4,
            'trato_personal' => 5
        ]);

        // Verificar que el paciente ya NO está en lista de salidas
        $pacientesListosDespues = Factura::where('estado', 'pagado')
            ->whereHas('consulta', function($q) {
                $q->where('estado', 'completada');
            })
            ->whereDoesntHave('consulta.encuestaSatisfaccion')
            ->get();
        
        $this->assertFalse($pacientesListosDespues->contains('consulta_id', $consulta->id));
    }

    /** @test */
    public function no_se_puede_procesar_salida_sin_encuesta()
    {
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);
        
        // Crear recepcionista
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);
        
        // Crear paciente
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta completada
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Control',
            'motivo_consulta' => 'Control',
            'estado' => 'completada'
        ]);

        // Crear factura pagada
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000003',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pagado',
            'fecha_emision' => now(),
            'fecha_pago' => now()
        ]);

        // Autenticarse como recepcionista
        $this->actingAs($recepcionista);

        // Intentar procesar salida sin completar encuesta
        $response = $this->post("/recepcion/pacientes/{$paciente->id}/confirmar-salida", []);

        // Verificar que hay errores de validación
        $response->assertSessionHasErrors(['atencion_medica', 'tiempo_espera', 'trato_personal']);

        // Verificar que NO se creó la encuesta
        $this->assertDatabaseMissing('encuestas_satisfaccion', [
            'paciente_id' => $paciente->id
        ]);
    }
}

