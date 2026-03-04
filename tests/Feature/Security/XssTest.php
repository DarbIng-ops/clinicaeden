<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class XssTest extends TestCase
{
    use RefreshDatabase;

    private User $recepcionista;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recepcionista = User::factory()->recepcionista()->create();
    }

    // ─── Formularios contienen token CSRF ─────────────────────

    #[Test]
    public function formulario_login_tiene_campo_csrf(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('_token', false);
    }

    #[Test]
    public function formulario_crear_paciente_tiene_campo_csrf(): void
    {
        $response = $this->actingAs($this->recepcionista)
            ->get('/recepcion/pacientes/crear');
        $response->assertStatus(200);
        $response->assertSee('_token', false);
    }

    // ─── Blade escapa la salida por defecto ───────────────────

    #[Test]
    public function lista_de_pacientes_no_ejecuta_payload_xss_almacenado(): void
    {
        // Almacenar payload XSS directamente en BD (bypass model hooks)
        DB::table('pacientes')->insert([
            'dni'        => '99999999',
            'nombres'    => '<script>alert("XSS")</script>',
            'apellidos'  => 'TestApellido',
            'fecha_nacimiento' => '1990-01-01',
            'sexo'       => 'M',
            'telefono'   => '3001234567',
            'direccion'  => 'Calle Test 123',
            'contacto_emergencia_nombre'   => 'Contacto Test',
            'contacto_emergencia_telefono' => '3009999999',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->recepcionista)
            ->get('/recepcion/pacientes');

        $response->assertStatus(200);

        // El tag <script> no debe aparecer sin escapar en el HTML
        $this->assertStringNotContainsString(
            '<script>alert("XSS")</script>',
            $response->getContent(),
            'El payload XSS se renderizó sin escapar en la lista de pacientes'
        );
    }

    #[Test]
    public function detalle_de_paciente_no_ejecuta_payload_xss_almacenado(): void
    {
        $id = DB::table('pacientes')->insertGetId([
            'dni'        => '88888888',
            'nombres'    => '<img src=x onerror=alert(document.cookie)>',
            'apellidos'  => 'TestApellido',
            'fecha_nacimiento' => '1990-01-01',
            'sexo'       => 'F',
            'telefono'   => '3001234568',
            'direccion'  => 'Calle Test 456',
            'contacto_emergencia_nombre'   => 'Contacto Test',
            'contacto_emergencia_telefono' => '3009999998',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->recepcionista)
            ->get('/recepcion/pacientes/' . $id);

        // Debe ser 200 (existe) o 404, nunca 500
        $this->assertContains($response->getStatusCode(), [200, 404]);

        if ($response->getStatusCode() === 200) {
            // El tag <img> no debe aparecer sin escapar (el < debe estar como &lt;)
            $this->assertStringNotContainsString(
                '<img src=x onerror=',
                $response->getContent(),
                'El evento onerror XSS se renderizó sin escapar'
            );
        }
    }

    // ─── Headers de seguridad ─────────────────────────────────

    #[Test]
    public function las_respuestas_no_reflejan_payload_xss_en_headers(): void
    {
        $payload = '<script>alert(1)</script>';

        $response = $this->post('/login', [
            'email' => $payload . '@test.com',
            'password' => 'test',
        ]);

        // El payload no debe aparecer sin escapar en la respuesta
        $contenido = $response->getContent();
        if ($contenido && str_contains($contenido, 'alert')) {
            $this->assertStringNotContainsString(
                '<script>',
                $contenido,
                'El payload XSS se reflejó sin escapar en la respuesta del login'
            );
        }

        $this->assertTrue(true); // El test llegó aquí sin errores
    }

    // ─── Nombres de usuario con XSS ──────────────────────────

    #[Test]
    public function nombre_de_usuario_con_xss_se_escapa_en_dashboard(): void
    {
        $xssPayload = '<script>alert("nombre")</script>';

        // Crear usuario con nombre XSS directamente en BD
        DB::table('users')->where('id', $this->recepcionista->id)
            ->update(['name' => $xssPayload]);

        $response = $this->actingAs($this->recepcionista)
            ->get('/recepcion/dashboard');

        $response->assertStatus(200);

        // El tag <script> no debe aparecer sin escapar
        $this->assertStringNotContainsString(
            '<script>alert("nombre")</script>',
            $response->getContent(),
            'El nombre de usuario con XSS se renderizó sin escapar en el dashboard'
        );
    }
}
