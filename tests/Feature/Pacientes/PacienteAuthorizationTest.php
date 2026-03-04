<?php

namespace Tests\Feature\Pacientes;

use App\Models\User;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PacienteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Acceso no autenticado ────────────────────────────────

    #[Test]
    public function invitado_no_puede_ver_lista_de_pacientes(): void
    {
        $response = $this->get('/recepcion/pacientes');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function invitado_no_puede_ver_formulario_crear_paciente(): void
    {
        $response = $this->get('/recepcion/pacientes/crear');
        $response->assertRedirect('/login');
    }

    // ─── Recepcionista: acceso permitido ─────────────────────

    #[Test]
    public function recepcionista_puede_ver_lista_de_pacientes(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $response = $this->actingAs($recepcionista)->get('/recepcion/pacientes');
        $response->assertStatus(200);
    }

    #[Test]
    public function recepcionista_puede_ver_formulario_crear_paciente(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $response = $this->actingAs($recepcionista)->get('/recepcion/pacientes/crear');
        $response->assertStatus(200);
    }

    #[Test]
    public function recepcionista_puede_ver_detalle_de_paciente(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $paciente = Paciente::factory()->create();

        $response = $this->actingAs($recepcionista)
            ->get('/recepcion/pacientes/' . $paciente->id);

        $response->assertStatus(200);
    }

    // ─── Otros roles: acceso denegado a módulo de recepción ──

    #[Test]
    public function medico_general_no_puede_acceder_a_gestion_de_pacientes_recepcion(): void
    {
        $medico = User::factory()->medicoGeneral()->create();
        $response = $this->actingAs($medico)->get('/recepcion/pacientes');
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function caja_no_puede_acceder_a_gestion_de_pacientes_recepcion(): void
    {
        $caja = User::factory()->caja()->create();
        $response = $this->actingAs($caja)->get('/recepcion/pacientes');
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function jefe_enfermeria_no_puede_crear_pacientes_via_recepcion(): void
    {
        $jefe = User::factory()->jefeEnfermeria()->create();
        $response = $this->actingAs($jefe)->get('/recepcion/pacientes/crear');
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function auxiliar_enfermeria_no_puede_acceder_a_recepcion_pacientes(): void
    {
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();
        $response = $this->actingAs($auxiliar)->get('/recepcion/pacientes');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Admin: acceso a gestión de usuarios ─────────────────

    #[Test]
    public function admin_puede_ver_lista_de_usuarios(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get('/admin/usuarios');
        $response->assertStatus(200);
    }

    #[Test]
    public function recepcionista_no_puede_ver_lista_de_usuarios_admin(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $response = $this->actingAs($recepcionista)->get('/admin/usuarios');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Médico: acceso a sus propios pacientes ───────────────

    #[Test]
    public function medico_general_puede_ver_sus_pacientes(): void
    {
        $medico = User::factory()->medicoGeneral()->create();
        $response = $this->actingAs($medico)->get('/medico-general/pacientes');
        $response->assertStatus(200);
    }

    #[Test]
    public function recepcionista_no_puede_ver_pacientes_en_modulo_medico(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $response = $this->actingAs($recepcionista)->get('/medico-general/pacientes');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Paciente inaccesible si usuario inactivo ─────────────

    #[Test]
    public function recepcionista_inactivo_no_puede_ver_pacientes(): void
    {
        $recepcionista = User::factory()->recepcionista()->inactivo()->create();
        $response = $this->actingAs($recepcionista)->get('/recepcion/pacientes');
        $response->assertRedirect(route('login'));
    }
}
