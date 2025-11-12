<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * CP-010: Crear nuevo usuario
 * 
 * Verificar que admin puede crear usuarios.
 */
class CrearUsuarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_puede_crear_usuario_con_datos_validos()
    {
        // Precondiciones: Usuario admin autenticado
        
        // Crear admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        // Autenticarse como admin
        $this->actingAs($admin);

        // Paso 1: Ir a Gestión de Usuarios
        // Paso 2: Clic en Nuevo Usuario
        // Paso 3: Llenar datos
        // Paso 4: Asignar rol
        $response = $this->post('/admin/usuarios', [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'email' => 'juan.perez@clinicaeden.com',
            'password' => 'password123',
            'role' => 'medico_general'
        ]);

        // Verificar que se creó el usuario
        $this->assertDatabaseHas('users', [
            'email' => 'juan.perez@clinicaeden.com',
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '12345678',
            'role' => 'medico_general'
        ]);

        // Verificar que el usuario puede hacer login
        $usuario = User::where('email', 'juan.perez@clinicaeden.com')->first();
        $this->assertTrue(Hash::check('password123', $usuario->password));

        // Verificar redirección
        $response->assertRedirect(route('admin.usuarios'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function admin_puede_crear_usuario_con_todos_los_roles_disponibles()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $roles = [
            'admin',
            'recepcionista',
            'medico_general',
            'medico_especialista',
            'jefe_enfermeria',
            'auxiliar_enfermeria',
            'caja'
        ];

        foreach ($roles as $index => $rol) {
            $this->post('/admin/usuarios', [
                'name' => 'Usuario',
                'apellido' => 'Test',
                'dni' => '1234567' . $index,
                'email' => "usuario{$index}@clinicaeden.com",
                'password' => 'password123',
                'role' => $rol
            ]);

            // Verificar que se creó
            $this->assertDatabaseHas('users', [
                'email' => "usuario{$index}@clinicaeden.com",
                'role' => $rol
            ]);
        }
    }

    /** @test */
    public function admin_puede_crear_medico_especialista_con_especialidad()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/usuarios', [
            'name' => 'Dr.',
            'apellido' => 'Especialista',
            'dni' => '87654321',
            'email' => 'especialista@clinicaeden.com',
            'password' => 'password123',
            'role' => 'medico_especialista',
            'especialidad' => 'Cardiología'
        ]);

        // Verificar que se creó con especialidad
        $this->assertDatabaseHas('users', [
            'email' => 'especialista@clinicaeden.com',
            'role' => 'medico_especialista',
            'especialidad' => 'Cardiología'
        ]);

        $response->assertRedirect(route('admin.usuarios'));
    }

    /** @test */
    public function no_se_puede_crear_usuario_con_email_duplicado()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        // Crear usuario existente
        User::factory()->create([
            'email' => 'existente@clinicaeden.com'
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario con email duplicado
        $response = $this->post('/admin/usuarios', [
            'name' => 'Usuario',
            'apellido' => 'Duplicado',
            'dni' => '11223344',
            'email' => 'existente@clinicaeden.com',
            'password' => 'password123',
            'role' => 'recepcionista'
        ]);

        // Verificar que hay error de validación
        $response->assertSessionHasErrors('email');
        
        // Verificar que solo existe un usuario con ese email
        $count = User::where('email', 'existente@clinicaeden.com')->count();
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function no_se_puede_crear_usuario_con_dni_duplicado()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        // Crear usuario existente
        User::factory()->create([
            'dni' => '99988877'
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario con DNI duplicado
        $response = $this->post('/admin/usuarios', [
            'name' => 'Usuario',
            'apellido' => 'DNI Duplicado',
            'dni' => '99988877',
            'email' => 'otro@clinicaeden.com',
            'password' => 'password123',
            'role' => 'medico_general'
        ]);

        // Verificar que hay error de validación
        $response->assertSessionHasErrors('dni');
        
        // Verificar que solo existe un usuario con ese DNI
        $count = User::where('dni', '99988877')->count();
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function no_se_puede_crear_usuario_con_password_corto()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario con password muy corto
        $response = $this->post('/admin/usuarios', [
            'name' => 'Usuario',
            'apellido' => 'Corto',
            'dni' => '55443322',
            'email' => 'corto@clinicaeden.com',
            'password' => '123',  // Muy corto
            'role' => 'medico_general'
        ]);

        // Verificar que hay error de validación
        $response->assertSessionHasErrors('password');
        
        // Verificar que NO se creó el usuario
        $this->assertDatabaseMissing('users', [
            'email' => 'corto@clinicaeden.com'
        ]);
    }

    /** @test */
    public function usuario_creado_puede_hacer_login()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Crear usuario nuevo
        $response = $this->post('/admin/usuarios', [
            'name' => 'Nuevo',
            'apellido' => 'Usuario',
            'dni' => '11223344',
            'email' => 'nuevo@clinicaeden.com',
            'password' => 'password123',
            'role' => 'auxiliar_enfermeria'
        ]);

        $response->assertRedirect();

        // Cerrar sesión de admin
        $this->post('/logout');

        // Intentar login con el nuevo usuario
        $loginResponse = $this->post('/login', [
            'email' => 'nuevo@clinicaeden.com',
            'password' => 'password123'
        ]);

        $this->assertAuthenticated();
        $loginResponse->assertRedirect('/dashboard');
    }

    /** @test */
    public function admin_no_puede_crear_usuario_sin_datos_obligatorios()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar crear usuario sin datos
        $response = $this->post('/admin/usuarios', []);

        // Verificar errores de validación para todos los campos obligatorios
        $response->assertSessionHasErrors(['name', 'apellido', 'dni', 'email', 'password', 'role']);
    }
}

