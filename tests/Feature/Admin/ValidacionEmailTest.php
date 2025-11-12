<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CP-015: Formato de email
 * 
 * Verificar validación de formato de email.
 */
class ValidacionEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function crear_usuario_acepta_email_valido()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => 'juan.perez@example.com',
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'juan.perez@example.com'
        ]);
    }

    /** @test */
    public function crear_usuario_rechaza_email_sin_arroba()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => 'juanperezexample.com', // Email sin @
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function crear_usuario_rechaza_email_sin_dominio()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => 'juanperez@', // Email sin dominio
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function crear_usuario_rechaza_email_sin_usuario()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => '@example.com', // Email sin usuario
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function crear_usuario_rechaza_email_con_espacios()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => 'juan perez@example.com', // Email con espacios
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function crear_paciente_acepta_email_valido()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        $response = $this->post('/recepcion/pacientes', [
            'dni' => '12345678',
            'nombres' => 'María',
            'apellidos' => 'González',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'F',
            'telefono' => '0987654321',
            'email' => 'maria.gonzalez@example.com',
            'direccion' => 'Calle 123',
            'ciudad' => 'Ciudad',
            'contacto_emergencia_nombre' => 'Juan González',
            'contacto_emergencia_telefono' => '0987654321'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pacientes', [
            'email' => 'maria.gonzalez@example.com'
        ]);
    }

    /** @test */
    public function crear_paciente_rechaza_email_invalido()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        $response = $this->post('/recepcion/pacientes', [
            'dni' => '12345678',
            'nombres' => 'María',
            'apellidos' => 'González',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'F',
            'telefono' => '0987654321',
            'email' => 'email-invalido', // Email inválido
            'direccion' => 'Calle 123',
            'ciudad' => 'Ciudad',
            'contacto_emergencia_nombre' => 'Juan González',
            'contacto_emergencia_telefono' => '0987654321'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function crear_paciente_rechaza_email_con_caracteres_especiales_invalidos()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        $response = $this->post('/recepcion/pacientes', [
            'dni' => '12345678',
            'nombres' => 'María',
            'apellidos' => 'González',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'F',
            'telefono' => '0987654321',
            'email' => 'maria@example..com', // Email con puntos dobles
            'direccion' => 'Calle 123',
            'ciudad' => 'Ciudad',
            'contacto_emergencia_nombre' => 'Juan González',
            'contacto_emergencia_telefono' => '0987654321'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function crear_usuario_acepta_emails_variados_validos()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $emailsValidos = [
            'usuario@example.com',
            'usuario.nombre@example.com',
            'usuario123@example.com',
            'usuario_apellido@example.com',
            'usuario+tag@example.com',
            'usuario@sub.example.com',
            'u@e.com',
            'test.email.with.dots@example-domain.com'
        ];

        foreach ($emailsValidos as $index => $email) {
            $response = $this->post('/admin/usuarios', [
                'name' => "Usuario{$index}",
                'apellido' => "Apellido{$index}",
                'dni' => "1234567{$index}",
                'email' => $email,
                'password' => 'password123',
                'role' => 'recepcionista'
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('users', [
                'email' => $email
            ]);
        }
    }

    /** @test */
    public function crear_usuario_rechaza_emails_invalidos_varios()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $emailsInvalidos = [
            'sin.arroba.com',
            '@sindominio.com',
            'sinusuario@.com',
            'con espacios@example.com',
            'muchos@@puntos@example.com',
            'sin.com'
        ];

        foreach ($emailsInvalidos as $index => $email) {
            $response = $this->post('/admin/usuarios', [
                'name' => "Usuario{$index}",
                'apellido' => "Apellido{$index}",
                'dni' => "1234567{$index}",
                'email' => $email,
                'password' => 'password123',
                'role' => 'recepcionista'
            ]);

            $response->assertSessionHasErrors(['email']);
        }
    }

    /** @test */
    public function actualizar_usuario_rechaza_email_invalido()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $usuario = User::factory()->create([
            'email' => 'original@example.com'
        ]);

        $this->actingAs($admin);

        $response = $this->put("/admin/usuarios/{$usuario->id}", [
            'name' => $usuario->name,
            'apellido' => $usuario->apellido,
            'dni' => $usuario->dni,
            'email' => 'email-invalido',
            'role' => $usuario->role
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}

