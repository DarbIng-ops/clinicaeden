<?php

namespace Tests\Feature\Pacientes;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PacienteCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function recepcionista_puede_crear_paciente_con_datos_validos()
    {
        // Crear recepcionista
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        // Autenticarse como recepcionista
        $this->actingAs($recepcionista);

        // Crear paciente
        $response = $this->post('/recepcion/pacientes', [
            'dni' => '12345678',
            'nombres' => 'Juan Carlos',
            'apellidos' => 'Pérez García',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'M',
            'telefono' => '3001234567',
            'email' => 'juan.perez@email.com',
            'direccion' => 'Calle 123 #45-67',
            'ciudad' => 'Bogotá',
            'contacto_emergencia_nombre' => 'María Pérez',
            'contacto_emergencia_telefono' => '3009876543'
        ]);

        // Verificar que se creó
        $response->assertRedirect('/recepcion/pacientes');
        $this->assertDatabaseHas('pacientes', [
            'dni' => '12345678',
            'nombres' => 'Juan Carlos'
        ]);
    }

     /** @test */
     public function no_se_puede_crear_paciente_con_dni_duplicado()
     {
         // Crear recepcionista
         $recepcionista = User::factory()->create([
             'role' => 'recepcionista',
             'activo' => 1
         ]);
 
         $this->actingAs($recepcionista);
 
         // Crear primer paciente con DNI 12345678
         Paciente::factory()->create(['dni' => '12345678']);
 
         // Intentar crear segundo paciente con mismo DNI
         $response = $this->post('/recepcion/pacientes', [
             'dni' => '12345678',
             'nombres' => 'Pedro',
             'apellidos' => 'González',
             'fecha_nacimiento' => '1995-03-20',
             'sexo' => 'M',
             'telefono' => '3001111111',
             'email' => 'pedro@email.com',
             'direccion' => 'Calle 1',
             'ciudad' => 'Cali',
             'contacto_emergencia_nombre' => 'Ana',
             'contacto_emergencia_telefono' => '3002222222'
         ]);
 
         // Verificar que dio error
         $response->assertSessionHasErrors('dni');
         $this->assertEquals(1, Paciente::where('dni', '12345678')->count());
     }
     
}