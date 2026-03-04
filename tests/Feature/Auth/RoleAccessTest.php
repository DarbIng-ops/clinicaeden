<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    // ─── Admin ───────────────────────────────────────────────

    #[Test]
    public function admin_puede_acceder_al_dashboard_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    #[Test]
    public function recepcionista_no_puede_acceder_al_dashboard_admin(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $response = $this->actingAs($recepcionista)->get('/admin/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function medico_general_no_puede_acceder_al_dashboard_admin(): void
    {
        $medico = User::factory()->medicoGeneral()->create();
        $response = $this->actingAs($medico)->get('/admin/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function caja_no_puede_acceder_al_panel_de_usuarios_admin(): void
    {
        $caja = User::factory()->caja()->create();
        $response = $this->actingAs($caja)->get('/admin/usuarios');
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function jefe_enfermeria_no_puede_acceder_a_reportes_admin(): void
    {
        $jefe = User::factory()->jefeEnfermeria()->create();
        $response = $this->actingAs($jefe)->get('/admin/reportes');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Recepcionista ────────────────────────────────────────

    #[Test]
    public function recepcionista_puede_acceder_al_dashboard_recepcion(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $response = $this->actingAs($recepcionista)->get('/recepcion/dashboard');
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_no_puede_acceder_al_dashboard_recepcion(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get('/recepcion/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function medico_general_no_puede_acceder_al_dashboard_recepcion(): void
    {
        $medico = User::factory()->medicoGeneral()->create();
        $response = $this->actingAs($medico)->get('/recepcion/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Médico General ───────────────────────────────────────

    #[Test]
    public function medico_general_puede_acceder_a_su_dashboard(): void
    {
        $medico = User::factory()->medicoGeneral()->create();
        $response = $this->actingAs($medico)->get('/medico-general/dashboard');
        $response->assertStatus(200);
    }

    #[Test]
    public function recepcionista_no_puede_acceder_al_dashboard_medico(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $response = $this->actingAs($recepcionista)->get('/medico-general/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function auxiliar_no_puede_acceder_al_dashboard_medico(): void
    {
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();
        $response = $this->actingAs($auxiliar)->get('/medico-general/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Caja ────────────────────────────────────────────────

    #[Test]
    public function caja_puede_acceder_a_su_dashboard(): void
    {
        $caja = User::factory()->caja()->create();
        $response = $this->actingAs($caja)->get('/caja/dashboard');
        $response->assertStatus(200);
    }

    #[Test]
    public function medico_no_puede_acceder_al_dashboard_caja(): void
    {
        $medico = User::factory()->medicoGeneral()->create();
        $response = $this->actingAs($medico)->get('/caja/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Jefe Enfermería ─────────────────────────────────────

    #[Test]
    public function jefe_enfermeria_puede_acceder_a_su_dashboard(): void
    {
        $jefe = User::factory()->jefeEnfermeria()->create();
        $response = $this->actingAs($jefe)->get('/jefe-enfermeria/dashboard');
        $response->assertStatus(200);
    }

    #[Test]
    public function auxiliar_no_puede_acceder_al_dashboard_jefe_enfermeria(): void
    {
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();
        $response = $this->actingAs($auxiliar)->get('/jefe-enfermeria/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Auxiliar Enfermería ─────────────────────────────────

    #[Test]
    public function auxiliar_puede_acceder_a_su_dashboard(): void
    {
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();
        $response = $this->actingAs($auxiliar)->get('/auxiliar-enfermeria/dashboard');
        $response->assertStatus(200);
    }

    #[Test]
    public function recepcionista_no_puede_acceder_al_dashboard_auxiliar(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();
        $response = $this->actingAs($recepcionista)->get('/auxiliar-enfermeria/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    // ─── Protección global ────────────────────────────────────

    #[Test]
    public function todos_los_dashboards_protegidos_redirigen_a_invitados(): void
    {
        $rutas = [
            '/admin/dashboard',
            '/recepcion/dashboard',
            '/medico-general/dashboard',
            '/caja/dashboard',
            '/jefe-enfermeria/dashboard',
            '/auxiliar-enfermeria/dashboard',
        ];

        foreach ($rutas as $ruta) {
            $response = $this->get($ruta);
            $response->assertRedirect('/login',
                "La ruta $ruta no redirigió a /login para invitados");
        }
    }
}
