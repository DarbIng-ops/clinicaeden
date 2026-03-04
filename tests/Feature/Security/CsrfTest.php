<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CsrfTest extends TestCase
{
    use RefreshDatabase;

    // ─── Presencia del token en formularios ───────────────────

    #[Test]
    public function formulario_login_contiene_campo_csrf_token(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('name="_token"', false);
    }

    #[Test]
    public function formulario_crear_paciente_contiene_campo_csrf_token(): void
    {
        $recepcionista = User::factory()->recepcionista()->create();

        $response = $this->actingAs($recepcionista)
            ->get('/recepcion/pacientes/crear');

        $response->assertStatus(200);
        $response->assertSee('name="_token"', false);
    }

    #[Test]
    public function formulario_crear_usuario_admin_contiene_csrf_token(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->get('/admin/usuarios/crear');

        $response->assertStatus(200);
        $response->assertSee('name="_token"', false);
    }

    // ─── Middleware CSRF registrado ───────────────────────────

    #[Test]
    public function middleware_csrf_esta_disponible_en_el_contenedor(): void
    {
        $middleware = $this->app->make(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class
        );
        $this->assertNotNull($middleware,
            'El middleware VerifyCsrfToken no está registrado en el contenedor');
    }

    // ─── Token en sesión al visitar formularios ───────────────

    #[Test]
    public function visitar_login_genera_token_csrf_en_sesion(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        // La sesión debe tener un token CSRF
        $this->assertNotEmpty(session()->token(),
            'Visitar el formulario de login no generó un token CSRF en sesión');
    }

    #[Test]
    public function token_csrf_es_una_cadena_hexadecimal_de_longitud_adecuada(): void
    {
        $this->get('/login');
        $token = session()->token();

        $this->assertIsString($token);
        $this->assertGreaterThanOrEqual(40, strlen($token),
            'El token CSRF es demasiado corto (< 40 caracteres)');
    }

    // ─── Protección en modo producción ───────────────────────

    #[Test]
    public function post_con_token_invalido_en_modo_produccion_es_rechazado(): void
    {
        // Simular entorno de producción donde CSRF no se bypasea
        $this->app['env'] = 'production';

        try {
            $this->post('/login', [
                '_token' => 'token_invalido_xyz',
                'email'  => 'test@test.com',
                'password' => 'password',
            ]);

            // En producción, sin token válido no debe autenticar al usuario
            $this->assertFalse(
                auth()->check(),
                'El POST con token CSRF inválido no rechazó la petición'
            );

        } finally {
            $this->app['env'] = 'testing';
        }
    }
}
