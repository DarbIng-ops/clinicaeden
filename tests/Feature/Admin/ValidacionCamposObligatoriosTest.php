<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\HistoriaClinica;
use App\Models\Factura;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CP-014: Validación de campos obligatorios
 * 
 * Verificar que formularios validan campos requeridos.
 */
class ValidacionCamposObligatoriosTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function crear_usuario_validacion_campos_obligatorios()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario sin campos obligatorios
        $response = $this->post('/admin/usuarios', []);

        // Verificar que se muestran errores de validación
        $response->assertSessionHasErrors(['name', 'apellido', 'dni', 'email', 'password', 'role']);
    }

    /** @test */
    public function crear_usuario_validacion_nombre_obligatorio()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario sin nombre
        $response = $this->post('/admin/usuarios', [
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function crear_usuario_validacion_email_obligatorio()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario sin email
        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function crear_usuario_validacion_password_obligatorio()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario sin password
        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => 'test@example.com',
            'role' => 'recepcionista'
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function crear_usuario_validacion_dni_obligatorio()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario sin DNI
        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        $response->assertSessionHasErrors(['dni']);
    }

    /** @test */
    public function crear_usuario_validacion_rol_obligatorio()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario sin rol
        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    /** @test */
    public function crear_paciente_validacion_campos_obligatorios()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        // Intentar crear paciente sin campos obligatorios
        $response = $this->post('/recepcion/pacientes', []);

        // Verificar que se muestran errores de validación
        $response->assertSessionHasErrors([
            'dni', 'nombres', 'apellidos', 'fecha_nacimiento', 
            'sexo', 'telefono', 'email', 'direccion', 'ciudad',
            'contacto_emergencia_nombre', 'contacto_emergencia_telefono'
        ]);
    }

    /** @test */
    public function crear_paciente_validacion_dni_obligatorio()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        // Intentar crear paciente sin DNI
        $response = $this->post('/recepcion/pacientes', [
            'nombres' => 'María',
            'apellidos' => 'González',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'F',
            'telefono' => '0987654321',
            'email' => 'maria@example.com',
            'direccion' => 'Calle 123',
            'ciudad' => 'Ciudad',
            'contacto_emergencia_nombre' => 'Juan González',
            'contacto_emergencia_telefono' => '0987654321'
        ]);

        $response->assertSessionHasErrors(['dni']);
    }

    /** @test */
    public function crear_paciente_validacion_nombres_obligatorio()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        // Intentar crear paciente sin nombres
        $response = $this->post('/recepcion/pacientes', [
            'dni' => '12345678',
            'apellidos' => 'González',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'F',
            'telefono' => '0987654321',
            'email' => 'maria@example.com',
            'direccion' => 'Calle 123',
            'ciudad' => 'Ciudad',
            'contacto_emergencia_nombre' => 'Juan González',
            'contacto_emergencia_telefono' => '0987654321'
        ]);

        $response->assertSessionHasErrors(['nombres']);
    }

    /** @test */
    public function crear_paciente_validacion_email_obligatorio()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        // Intentar crear paciente sin email
        $response = $this->post('/recepcion/pacientes', [
            'dni' => '12345678',
            'nombres' => 'María',
            'apellidos' => 'González',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'F',
            'telefono' => '0987654321',
            'direccion' => 'Calle 123',
            'ciudad' => 'Ciudad',
            'contacto_emergencia_nombre' => 'Juan González',
            'contacto_emergencia_telefono' => '0987654321'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function finalizar_consulta_validacion_campos_obligatorios()
    {
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        $paciente = Paciente::factory()->create();
        $historiaClinica = HistoriaClinica::create(['paciente_id' => $paciente->id]);
        $consulta = Consulta::factory()->create([
            'historia_clinica_id' => $historiaClinica->id,
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'estado' => 'pendiente',
            'motivo' => 'Dolor de cabeza'
        ]);

        $this->actingAs($medico);

        // Intentar finalizar consulta sin campos obligatorios
        $response = $this->post("/medico-general/consultas/{$consulta->id}/finalizar", []);

        $response->assertSessionHasErrors(['diagnostico', 'tratamiento', 'accion']);
    }

    /** @test */
    public function finalizar_consulta_validacion_diagnostico_obligatorio()
    {
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        $paciente = Paciente::factory()->create();
        $historiaClinica = HistoriaClinica::create(['paciente_id' => $paciente->id]);
        $consulta = Consulta::factory()->create([
            'historia_clinica_id' => $historiaClinica->id,
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'estado' => 'pendiente',
            'motivo' => 'Dolor de cabeza'
        ]);

        $this->actingAs($medico);

        // Intentar finalizar consulta sin diagnóstico
        $response = $this->post("/medico-general/consultas/{$consulta->id}/finalizar", [
            'tratamiento' => 'Reposo',
            'accion' => 'caja'
        ]);

        $response->assertSessionHasErrors(['diagnostico']);
    }

    /** @test */
    public function finalizar_consulta_validacion_tratamiento_obligatorio()
    {
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        $paciente = Paciente::factory()->create();
        $historiaClinica = HistoriaClinica::create(['paciente_id' => $paciente->id]);
        $consulta = Consulta::factory()->create([
            'historia_clinica_id' => $historiaClinica->id,
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'estado' => 'pendiente',
            'motivo' => 'Dolor de cabeza'
        ]);

        $this->actingAs($medico);

        // Intentar finalizar consulta sin tratamiento
        $response = $this->post("/medico-general/consultas/{$consulta->id}/finalizar", [
            'diagnostico' => 'Cefalea tensional',
            'accion' => 'caja'
        ]);

        $response->assertSessionHasErrors(['tratamiento']);
    }

    /** @test */
    public function confirmar_pago_validacion_campos_obligatorios()
    {
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        $paciente = Paciente::factory()->create();
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'numero_factura' => 'FAC-00000001',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pendiente',
            'fecha_emision' => now()
        ]);

        $this->actingAs($caja);

        // Intentar confirmar pago sin campos obligatorios
        $response = $this->post("/caja/facturas/{$factura->id}/confirmar-pago", []);

        $response->assertSessionHasErrors(['metodo_pago', 'monto_recibido']);
    }

    /** @test */
    public function confirmar_pago_validacion_metodo_pago_obligatorio()
    {
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        $paciente = Paciente::factory()->create();
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'numero_factura' => 'FAC-00000001',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pendiente',
            'fecha_emision' => now()
        ]);

        $this->actingAs($caja);

        // Intentar confirmar pago sin método de pago
        $response = $this->post("/caja/facturas/{$factura->id}/confirmar-pago", [
            'monto_recibido' => 1000
        ]);

        $response->assertSessionHasErrors(['metodo_pago']);
    }

    /** @test */
    public function confirmar_pago_validacion_monto_obligatorio()
    {
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        $paciente = Paciente::factory()->create();
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'numero_factura' => 'FAC-00000001',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pendiente',
            'fecha_emision' => now()
        ]);

        $this->actingAs($caja);

        // Intentar confirmar pago sin monto
        $response = $this->post("/caja/facturas/{$factura->id}/confirmar-pago", [
            'metodo_pago' => 'efectivo'
        ]);

        $response->assertSessionHasErrors(['monto_recibido']);
    }

    /** @test */
    public function confirmar_salida_validacion_campos_obligatorios()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        $paciente = Paciente::factory()->create();
        $historiaClinica = HistoriaClinica::create(['paciente_id' => $paciente->id]);
        $consulta = Consulta::factory()->create([
            'historia_clinica_id' => $historiaClinica->id,
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'estado' => 'completada',
            'motivo' => 'Consulta general'
        ]);
        $factura = Factura::create([
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'numero_factura' => 'FAC-00000001',
            'subtotal' => 1000,
            'impuestos' => 0,
            'total' => 1000,
            'metodo_pago' => 'efectivo',
            'estado' => 'pagado',
            'fecha_emision' => now()
        ]);

        $this->actingAs($recepcionista);

        // Intentar confirmar salida sin campos obligatorios
        $response = $this->post("/recepcion/pacientes/{$paciente->id}/confirmar-salida", []);

        $response->assertSessionHasErrors(['atencion_medica', 'tiempo_espera', 'trato_personal']);
    }
}

