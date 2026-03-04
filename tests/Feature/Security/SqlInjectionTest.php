<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SqlInjectionTest extends TestCase
{
    use RefreshDatabase;

    // ─── Payloads comunes de inyección SQL ────────────────────

    public static function sqlInjectionEmailPayloads(): array
    {
        return [
            ["' OR '1'='1"],
            ["' OR 1=1 --"],
            ["admin'--"],
            ["' UNION SELECT null,null,null --"],
            ["'; DROP TABLE users; --"],
            ["\" OR \"\"=\""],
            ["1' ORDER BY 1--"],
        ];
    }

    // ─── Login: inyección en email ────────────────────────────

    #[Test]
    #[DataProvider('sqlInjectionEmailPayloads')]
    public function inyeccion_sql_en_email_no_autentica_usuario(string $payload): void
    {
        $this->post('/login', [
            'email' => $payload,
            'password' => 'cualquier_cosa',
        ]);

        $this->assertFalse(
            auth()->check(),
            "El payload SQL '$payload' bypaseó la autenticación"
        );
    }

    // ─── Login: inyección en password ────────────────────────

    #[Test]
    public function inyeccion_sql_en_password_no_autentica_usuario(): void
    {
        $user = User::factory()->admin()->create();

        $payloads = [
            "' OR '1'='1",
            "' OR 1=1 --",
            "'; --",
            "anything' --",
        ];

        foreach ($payloads as $payload) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => $payload,
            ]);

            $this->assertFalse(
                auth()->check(),
                "El payload SQL en password '$payload' bypaseó la autenticación"
            );
        }
    }

    // ─── Búsqueda de paciente por DNI ────────────────────────

    #[Test]
    public function inyeccion_sql_en_busqueda_dni_no_produce_error_500(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();

        $payloads = [
            "' OR 1=1 --",
            "'; DROP TABLE pacientes; --",
            "1 UNION SELECT * FROM users",
            "' OR '1'='1' --",
        ];

        foreach ($payloads as $payload) {
            $response = $this->actingAs($recepcionista)
                ->get('/recepcion/api/buscar-paciente-dni?dni=' . urlencode($payload));

            $this->assertNotEquals(
                500,
                $response->getStatusCode(),
                "Payload '$payload' causó error 500 en la búsqueda por DNI"
            );
        }
    }

    // ─── Parámetro de ruta ────────────────────────────────────

    #[Test]
    public function inyeccion_sql_en_parametro_de_ruta_no_produce_error_500(): void
    {
        $admin = User::factory()->admin()->create();

        // Intentar inyección en parámetro de ruta de usuario
        $response = $this->actingAs($admin)
            ->get('/admin/usuarios/999999');

        // Debe responder 404 (no encontrado), nunca 500
        $this->assertContains(
            $response->getStatusCode(),
            [200, 301, 302, 404, 403],
            'Un parámetro de ruta inválido causó un error 500 inesperado'
        );
    }

    // ─── Integridad de la BD en SQLite ───────────────────────

    #[Test]
    public function la_base_de_datos_mantiene_integridad_tras_payloads_sql(): void
    {
        $countAntes = User::count();

        // Intentar múltiples payloads de inyección
        $payloads = [
            "'; DROP TABLE users; --",
            "'; DELETE FROM users WHERE 1=1; --",
            "'; INSERT INTO users (name) VALUES ('hacked'); --",
        ];

        foreach ($payloads as $payload) {
            $this->post('/login', [
                'email' => $payload,
                'password' => 'test',
            ]);
        }

        // La cantidad de usuarios debe permanecer igual
        $countDespues = User::count();
        $this->assertEquals(
            $countAntes,
            $countDespues,
            'Los payloads SQL alteraron la cantidad de registros en la base de datos'
        );
    }
}
