<?php

namespace Tests\Feature\Security;

use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Verifica que las reglas de negocio por rol se apliquen correctamente:
 *  - Cajero: sólo facturas/pagos — sin acceso a historial médico ni consultas
 *  - Médico: sólo sus propias consultas/hospitalizaciones
 *  - Enfermería: sólo sus propias hospitalizaciones asignadas
 *  - Recepcionista: registro de pacientes — sin caja ni diagnósticos
 *  - Admin: acceso total
 *
 * Notas de comportamiento esperado:
 *  - RoleMiddleware deniega por rol → redirect (302) a /dashboard
 *  - Controller abort(403) deniega por ownership → 403
 */
class AutorizacionNegocioTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function crearHospitalizacion(int $medicoId, int $jefeId, int $auxiliarId): Hospitalizacion
    {
        $paciente = Paciente::factory()->create();

        // Jerarquía: Piso → Módulo → Habitación
        $pisoId = DB::table('pisos')->insertGetId([
            'numero'     => rand(1, 99),
            'nombre'     => 'Piso Test',
            'activo'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $moduloId = DB::table('modulos_enfermeria')->insertGetId([
            'piso_id'              => $pisoId,
            'nombre'               => 'Módulo Test',
            'tipo'                 => 'hospitalizacion_general',
            'jefe_enfermeria_id'   => $jefeId,   // necesario para puedeAccederHospitalizacion()
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        $habitacionId = DB::table('habitaciones')->insertGetId([
            'numero'     => 'H-' . rand(100, 999),
            'modulo_id'  => $moduloId,
            'tipo'       => 'general',
            'capacidad'  => 4,
            'disponible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Hospitalizacion::create([
            'paciente_id'            => $paciente->id,
            'habitacion_id'          => $habitacionId,
            'medico_general_id'      => $medicoId,
            'jefe_enfermeria_id'     => $jefeId,
            'auxiliar_enfermeria_id' => $auxiliarId,
            'fecha_ingreso'          => now(),
            'estado'                 => 'activo',
            'motivo_hospitalizacion' => 'Prueba de autorización',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // CAJERO — solo puede ver/pagar facturas
    // El RoleMiddleware redirige (302) cuando el rol no está permitido.
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function cajero_no_puede_acceder_a_consultas_medicas(): void
    {
        $cajero = User::factory()->caja()->create();

        $response = $this->actingAs($cajero)->get('/medico-general/consultas');

        // Middleware redirige a /dashboard al no tener el rol requerido
        $response->assertRedirect();
    }

    #[Test]
    public function cajero_no_puede_acceder_a_hospitalizaciones(): void
    {
        $cajero = User::factory()->caja()->create();

        $response = $this->actingAs($cajero)->get('/hospitalizaciones');

        $response->assertRedirect();
    }

    #[Test]
    public function cajero_puede_acceder_a_su_dashboard(): void
    {
        $cajero = User::factory()->caja()->create();

        $response = $this->actingAs($cajero)->get('/caja/dashboard');

        $response->assertStatus(200);
    }

    #[Test]
    public function cajero_no_puede_acceder_al_admin(): void
    {
        $cajero = User::factory()->caja()->create();

        $response = $this->actingAs($cajero)->get('/admin/dashboard');

        $response->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MÉDICO GENERAL — sólo sus propias hospitalizaciones
    // El ownership check en HospitalizacionController devuelve abort(403).
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function medico_no_puede_ver_hospitalizacion_ajena(): void
    {
        $medicoOwner = User::factory()->medicoGeneral()->create();
        $medicoOtro  = User::factory()->medicoGeneral()->create();
        $jefe        = User::factory()->jefeEnfermeria()->create();
        $auxiliar    = User::factory()->auxiliarEnfermeria()->create();

        $hospitalizacion = $this->crearHospitalizacion($medicoOwner->id, $jefe->id, $auxiliar->id);

        // medicoOtro intenta ver la hospitalización de medicoOwner → 403
        $response = $this->actingAs($medicoOtro)
            ->get("/hospitalizaciones/{$hospitalizacion->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function medico_puede_ver_su_propia_hospitalizacion(): void
    {
        $medico   = User::factory()->medicoGeneral()->create();
        $jefe     = User::factory()->jefeEnfermeria()->create();
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();

        $hospitalizacion = $this->crearHospitalizacion($medico->id, $jefe->id, $auxiliar->id);

        $response = $this->actingAs($medico)
            ->get("/hospitalizaciones/{$hospitalizacion->id}");

        $response->assertStatus(200);
    }

    #[Test]
    public function medico_no_puede_dar_alta_medica_en_hospitalizacion_ajena(): void
    {
        $medicoOwner = User::factory()->medicoGeneral()->create();
        $medicoOtro  = User::factory()->medicoGeneral()->create();
        $jefe        = User::factory()->jefeEnfermeria()->create();
        $auxiliar    = User::factory()->auxiliarEnfermeria()->create();

        $hospitalizacion = $this->crearHospitalizacion($medicoOwner->id, $jefe->id, $auxiliar->id);

        $response = $this->actingAs($medicoOtro)
            ->post("/hospitalizaciones/{$hospitalizacion->id}/alta-medica");

        $response->assertStatus(403);
    }

    #[Test]
    public function medico_puede_dar_alta_medica_en_su_hospitalizacion(): void
    {
        $medico   = User::factory()->medicoGeneral()->create();
        $jefe     = User::factory()->jefeEnfermeria()->create();
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();

        $hospitalizacion = $this->crearHospitalizacion($medico->id, $jefe->id, $auxiliar->id);

        $response = $this->actingAs($medico)
            ->post("/hospitalizaciones/{$hospitalizacion->id}/alta-medica");

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalizaciones', [
            'id'     => $hospitalizacion->id,
            'estado' => 'alta_medica',
        ]);
    }

    #[Test]
    public function medico_no_puede_acceder_a_caja(): void
    {
        $medico = User::factory()->medicoGeneral()->create();

        $response = $this->actingAs($medico)->get('/caja/dashboard');

        $response->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════════════════
    // JEFE DE ENFERMERÍA — sólo sus hospitalizaciones asignadas
    // Usa las rutas propias /jefe-enfermeria/... (JefeEnfermeriaController).
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function jefe_enfermeria_no_puede_ver_hospitalizacion_ajena(): void
    {
        $medico    = User::factory()->medicoGeneral()->create();
        $jefeOwner = User::factory()->jefeEnfermeria()->create();
        $jefeOtro  = User::factory()->jefeEnfermeria()->create();
        $auxiliar  = User::factory()->auxiliarEnfermeria()->create();

        $hospitalizacion = $this->crearHospitalizacion($medico->id, $jefeOwner->id, $auxiliar->id);

        // JefeEnfermeriaController redirige (302) al denegar por ownership
        $response = $this->actingAs($jefeOtro)
            ->get("/jefe-enfermeria/hospitalizaciones/{$hospitalizacion->id}");

        $response->assertRedirect();
    }

    #[Test]
    public function jefe_enfermeria_no_puede_dar_alta_enfermeria_en_hospitalizacion_ajena(): void
    {
        $medico    = User::factory()->medicoGeneral()->create();
        $jefeOwner = User::factory()->jefeEnfermeria()->create();
        $jefeOtro  = User::factory()->jefeEnfermeria()->create();
        $auxiliar  = User::factory()->auxiliarEnfermeria()->create();

        $hospitalizacion = $this->crearHospitalizacion($medico->id, $jefeOwner->id, $auxiliar->id);
        $hospitalizacion->update(['estado' => 'alta_medica']);

        // JefeEnfermeriaController redirige al negar por ownership
        $response = $this->actingAs($jefeOtro)
            ->post("/jefe-enfermeria/hospitalizaciones/{$hospitalizacion->id}/alta-enfermeria");

        $response->assertRedirect();
        // La hospitalizacion NO debe cambiar de estado
        $this->assertDatabaseHas('hospitalizaciones', [
            'id'     => $hospitalizacion->id,
            'estado' => 'alta_medica',
        ]);
    }

    #[Test]
    public function jefe_enfermeria_puede_dar_alta_enfermeria_en_su_hospitalizacion(): void
    {
        $medico   = User::factory()->medicoGeneral()->create();
        $jefe     = User::factory()->jefeEnfermeria()->create();
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();

        // La hospitalizacion debe estar en estado 'alta_medica' para que el jefe pueda dar alta_enfermeria
        $hospitalizacion = $this->crearHospitalizacion($medico->id, $jefe->id, $auxiliar->id);
        $hospitalizacion->update(['estado' => 'alta_medica']);

        $response = $this->actingAs($jefe)
            ->post("/jefe-enfermeria/hospitalizaciones/{$hospitalizacion->id}/alta-enfermeria");

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalizaciones', [
            'id'     => $hospitalizacion->id,
            'estado' => 'alta_enfermeria',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // RECEPCIONISTA — registro/alta de pacientes, sin caja ni diagnósticos
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function recepcionista_no_puede_acceder_a_caja(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();

        $response = $this->actingAs($recepcionista)->get('/caja/dashboard');

        $response->assertRedirect();
    }

    #[Test]
    public function recepcionista_no_puede_acceder_a_consultas_medicas(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();

        $response = $this->actingAs($recepcionista)->get('/medico-general/consultas');

        $response->assertRedirect();
    }

    #[Test]
    public function recepcionista_puede_acceder_a_listado_de_pacientes(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();

        $response = $this->actingAs($recepcionista)->get('/recepcion/pacientes');

        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ADMIN — acceso total, bypassa los checks de ownership
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_puede_ver_hospitalizacion_de_cualquier_medico(): void
    {
        $medico   = User::factory()->medicoGeneral()->create();
        $jefe     = User::factory()->jefeEnfermeria()->create();
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();
        $admin    = User::factory()->admin()->create();

        $hospitalizacion = $this->crearHospitalizacion($medico->id, $jefe->id, $auxiliar->id);

        $response = $this->actingAs($admin)
            ->get("/hospitalizaciones/{$hospitalizacion->id}");

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_puede_dar_alta_medica_en_cualquier_hospitalizacion(): void
    {
        $medico   = User::factory()->medicoGeneral()->create();
        $jefe     = User::factory()->jefeEnfermeria()->create();
        $auxiliar = User::factory()->auxiliarEnfermeria()->create();
        $admin    = User::factory()->admin()->create();

        $hospitalizacion = $this->crearHospitalizacion($medico->id, $jefe->id, $auxiliar->id);

        $response = $this->actingAs($admin)
            ->post("/hospitalizaciones/{$hospitalizacion->id}/alta-medica");

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalizaciones', [
            'id'     => $hospitalizacion->id,
            'estado' => 'alta_medica',
        ]);
    }
}
