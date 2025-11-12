<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CP-013: Control de acceso por rol
 * 
 * Verificar que usuarios solo accedan a módulos de su rol.
 */
class ControlAccesoRolTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_solo_accede_a_modulos_administrativos()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Debe poder acceder a módulos de admin
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/admin/usuarios');
        $response->assertStatus(200);

        $response = $this->get('/admin/reportes');
        $response->assertStatus(200);

        // NO debe poder acceder a módulos de otros roles
        $response = $this->get('/recepcion/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/medico-general/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/caja/dashboard');
        $response->assertStatus(302); // Redirect
    }

    /** @test */
    public function recepcionista_solo_accede_a_modulos_de_recepcion()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        // Debe poder acceder a módulos de recepcionista
        $response = $this->get('/recepcion/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/recepcion/pacientes');
        $response->assertStatus(200);

        // NO debe poder acceder a módulos de otros roles
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/medico-general/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/caja/dashboard');
        $response->assertStatus(302); // Redirect
    }

    /** @test */
    public function medico_general_solo_accede_a_modulos_medicos()
    {
        $medico = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        $this->actingAs($medico);

        // Debe poder acceder a módulos de médico general
        $response = $this->get('/medico-general/dashboard');
        $response->assertStatus(200);

        // NO debe poder acceder a módulos de otros roles
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/recepcion/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/medico-especialista/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/caja/dashboard');
        $response->assertStatus(302); // Redirect
    }

    /** @test */
    public function medico_especialista_solo_accede_a_modulos_medicos()
    {
        $medicoEspecialista = User::factory()->create([
            'role' => 'medico_especialista',
            'activo' => 1
        ]);

        $this->actingAs($medicoEspecialista);

        // Debe poder acceder a módulos de médico especialista
        $response = $this->get('/medico-especialista/dashboard');
        $response->assertStatus(200);

        // NO debe poder acceder a módulos de otros roles
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/recepcion/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/medico-general/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/caja/dashboard');
        $response->assertStatus(302); // Redirect
    }

    /** @test */
    public function jefe_enfermeria_solo_accede_a_modulos_de_enfermeria()
    {
        $jefeEnfermeria = User::factory()->create([
            'role' => 'jefe_enfermeria',
            'activo' => 1
        ]);

        $this->actingAs($jefeEnfermeria);

        // Debe poder acceder a módulos de jefe de enfermería
        $response = $this->get('/jefe-enfermeria/dashboard');
        $response->assertStatus(200);

        // NO debe poder acceder a módulos de otros roles
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/recepcion/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/medico-general/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/caja/dashboard');
        $response->assertStatus(302); // Redirect
    }

    /** @test */
    public function auxiliar_enfermeria_solo_accede_a_modulos_de_auxiliar()
    {
        $auxiliar = User::factory()->create([
            'role' => 'auxiliar_enfermeria',
            'activo' => 1
        ]);

        $this->actingAs($auxiliar);

        // Debe poder acceder a módulos de auxiliar de enfermería
        $response = $this->get('/auxiliar-enfermeria/dashboard');
        $response->assertStatus(200);

        // NO debe poder acceder a módulos de otros roles
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/recepcion/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/medico-general/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/caja/dashboard');
        $response->assertStatus(302); // Redirect
    }

    /** @test */
    public function caja_solo_accede_a_modulos_de_caja()
    {
        $caja = User::factory()->create([
            'role' => 'caja',
            'activo' => 1
        ]);

        $this->actingAs($caja);

        // Debe poder acceder a módulos de caja
        $response = $this->get('/caja/dashboard');
        $response->assertStatus(200);

        // NO debe poder acceder a módulos de otros roles
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/recepcion/dashboard');
        $response->assertStatus(302); // Redirect

        $response = $this->get('/medico-general/dashboard');
        $response->assertStatus(302); // Redirect
    }

    /** @test */
    public function usuario_sin_autenticacion_no_puede_acceder_a_ningun_modulo()
    {
        // Intentar acceder sin autenticación
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));

        $response = $this->get('/recepcion/dashboard');
        $response->assertRedirect(route('login'));

        $response = $this->get('/medico-general/dashboard');
        $response->assertRedirect(route('login'));

        $response = $this->get('/caja/dashboard');
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function intento_acceso_no_autorizado_muestra_mensaje_error()
    {
        $recepcionista = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 1
        ]);

        $this->actingAs($recepcionista);

        // Intentar acceder a módulo de admin
        $response = $this->get('/admin/dashboard');
        
        // Debe redirigir con mensaje de error
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'No tienes permiso para acceder a esta sección.');
    }

    /** @test */
    public function todos_los_usuarios_pueden_acceder_a_dashboard_general()
    {
        $roles = ['admin', 'recepcionista', 'medico_general', 'medico_especialista', 'jefe_enfermeria', 'auxiliar_enfermeria', 'caja'];

        foreach ($roles as $rol) {
            $usuario = User::factory()->create([
                'role' => $rol,
                'activo' => 1
            ]);

            $this->actingAs($usuario);

            // Todos los usuarios autenticados pueden acceder al dashboard general (redirige a su dashboard)
            $response = $this->get('/dashboard');
            $response->assertStatus(302); // Redirige al dashboard específico del rol
        }
    }

    /** @test */
    public function todos_los_usuarios_pueden_acceder_a_notificaciones()
    {
        $roles = ['admin', 'recepcionista', 'medico_general', 'medico_especialista', 'jefe_enfermeria', 'auxiliar_enfermeria', 'caja'];

        foreach ($roles as $rol) {
            $usuario = User::factory()->create([
                'role' => $rol,
                'activo' => 1
            ]);

            $this->actingAs($usuario);

            // Todos los usuarios autenticados pueden acceder a notificaciones
            $response = $this->get('/notificaciones');
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function usuarios_no_pueden_acceder_a_recursos_de_otros_roles()
    {
        // Crear varios usuarios con diferentes roles
        $recepcionista = User::factory()->create(['role' => 'recepcionista', 'activo' => 1]);
        $medico = User::factory()->create(['role' => 'medico_general', 'activo' => 1]);
        $caja = User::factory()->create(['role' => 'caja', 'activo' => 1]);
        $admin = User::factory()->create(['role' => 'admin', 'activo' => 1]);

        // Recepcionista NO puede gestionar usuarios
        $this->actingAs($recepcionista);
        $response = $this->get('/admin/usuarios');
        $response->assertStatus(302);

        // Médico NO puede crear pacientes
        $this->actingAs($medico);
        $response = $this->get('/recepcion/pacientes/crear');
        $response->assertStatus(302);

        // Caja NO puede ver consultas
        $this->actingAs($caja);
        $response = $this->get('/medico-general/consultas');
        $response->assertStatus(302);

        // Admin NO puede ver facturas de caja
        $this->actingAs($admin);
        $response = $this->get('/caja/facturas');
        $response->assertStatus(302);
    }
}

