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
 * CP-008: Procesar pago
 * 
 * Verificar que caja puede registrar un pago de factura.
 */
class ProcesarPagoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function caja_puede_registrar_pago_con_efectivo()
    {
        // Precondiciones: Factura pendiente en el sistema
        
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);
        
        // Crear paciente
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta y factura
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Dolor de cabeza',
            'motivo_consulta' => 'Dolor de cabeza',
            'estado' => 'completada'
        ]);

        // Crear factura pendiente
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000001',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pendiente',
            'fecha_emision' => now()
        ]);

        // Crear usuario de caja
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        // Autenticarse como caja
        $this->actingAs($caja);

        // Paso 1: Buscar factura
        // Paso 2: Seleccionar método de pago
        // Paso 3: Registrar pago
        $response = $this->post("/caja/facturas/{$factura->id}/confirmar-pago", [
            'metodo_pago' => 'efectivo',
            'monto_recibido' => 1000
        ]);

        // Verificar que la factura cambió a "pagada"
        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'estado' => 'pagado',
            'metodo_pago' => 'efectivo',
            'monto_recibido' => 1000,
            'caja_id' => $caja->id
        ]);

        // Verificar que tiene fecha de pago
        $facturaActualizada = Factura::find($factura->id);
        $this->assertNotNull($facturaActualizada->fecha_pago);

        // Verificar redirección
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function caja_puede_registrar_pago_con_tarjeta()
    {
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);
        
        // Crear paciente
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta y factura
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Fiebre',
            'motivo_consulta' => 'Fiebre',
            'estado' => 'completada'
        ]);

        // Crear factura pendiente
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000002',
            'subtotal' => 1500,
            'impuestos' => 0,
            'total' => 1500,
            'metodo_pago' => 'efectivo', // Valor temporal
            'estado' => 'pendiente',
            'fecha_emision' => now()
        ]);

        // Crear usuario de caja
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        // Autenticarse como caja
        $this->actingAs($caja);

        // Registrar pago con tarjeta
        $response = $this->post("/caja/facturas/{$factura->id}/confirmar-pago", [
            'metodo_pago' => 'tarjeta',
            'monto_recibido' => 1500
        ]);

        // Verificar que la factura cambió a "pagada" con método tarjeta
        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'estado' => 'pagado',
            'metodo_pago' => 'tarjeta',
            'monto_recibido' => 1500
        ]);

        // Verificar redirección
        $response->assertRedirect();
    }

    /** @test */
    public function caja_puede_registrar_pago_con_tarjeta_monto_alto()
    {
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);
        
        // Crear paciente
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta y factura
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Dolor de espalda',
            'motivo_consulta' => 'Dolor de espalda',
            'estado' => 'completada'
        ]);

        // Crear factura pendiente
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000003',
            'subtotal' => 2000,
            'impuestos' => 0,
            'total' => 2000,
            'metodo_pago' => 'efectivo', // Valor temporal
            'estado' => 'pendiente',
            'fecha_emision' => now()
        ]);

        // Crear usuario de caja
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        // Autenticarse como caja
        $this->actingAs($caja);

        // Registrar pago con transferencia
        $response = $this->post("/caja/facturas/{$factura->id}/confirmar-pago", [
            'metodo_pago' => 'tarjeta', // ConfirmarPago solo acepta efectivo o tarjeta
            'monto_recibido' => 2000
        ]);

        // Verificar que la factura cambió a "pagada" con método tarjeta
        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'estado' => 'pagado',
            'metodo_pago' => 'tarjeta',
            'monto_recibido' => 2000
        ]);

        // Verificar redirección
        $response->assertRedirect();
    }

    /** @test */
    public function caja_notifica_recepcion_al_procesar_pago()
    {
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);
        
        // Crear paciente
        $paciente = Paciente::factory()->create([
            'nombres' => 'María',
            'apellidos' => 'González',
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta y factura
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Control',
            'motivo_consulta' => 'Control',
            'estado' => 'completada'
        ]);

        // Crear factura pendiente
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000004',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pendiente',
            'fecha_emision' => now()
        ]);

        // Crear usuarios
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        // Autenticarse como caja
        $this->actingAs($caja);

        // Procesar pago
        $response = $this->post("/caja/facturas/{$factura->id}/confirmar-pago", [
            'metodo_pago' => 'efectivo',
            'monto_recibido' => 1000
        ]);

        // Verificar que se notificó a recepción
        $this->assertDatabaseHas('notificaciones_sistema', [
            'usuario_receptor_id' => $recepcionista->id,
            'tipo' => 'pago_confirmado',
            'leida' => false
        ]);

        // Verificar que la notificación contiene la información correcta
        $notificacion = \App\Models\NotificacionSistema::where('usuario_receptor_id', $recepcionista->id)
            ->where('tipo', 'pago_confirmado')
            ->first();
            
        $this->assertNotNull($notificacion);
        $this->assertEquals('Pago procesado', $notificacion->titulo);
        $this->assertStringContainsString('María González', $notificacion->mensaje);
    }

    /** @test */
    public function no_se_puede_registrar_pago_con_monto_menor()
    {
        // Crear médico
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);
        
        // Crear paciente
        $paciente = Paciente::factory()->create([
            'activo' => 1
        ]);
        
        $historiaClinica = HistoriaClinica::create([
            'paciente_id' => $paciente->id
        ]);

        // Crear consulta y factura
        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'historia_clinica_id' => $historiaClinica->id,
            'fecha_consulta' => now(),
            'motivo' => 'Consulta',
            'motivo_consulta' => 'Consulta',
            'estado' => 'completada'
        ]);

        // Crear factura pendiente
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000005',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pendiente',
            'fecha_emision' => now()
        ]);

        // Crear usuario de caja
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        // Autenticarse como caja
        $this->actingAs($caja);

        // Intentar registrar pago con monto menor
        $response = $this->post("/caja/facturas/{$factura->id}/confirmar-pago", [
            'metodo_pago' => 'efectivo',
            'monto_recibido' => 500  // Menor que el total de 1000
        ]);

        // Verificar que hay error de validación
        $response->assertSessionHasErrors('monto_recibido');

        // Verificar que la factura sigue pendiente
        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'estado' => 'pendiente'
        ]);
    }
}

