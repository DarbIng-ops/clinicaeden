<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_is_redirected_to_login_when_accessing_admin_route(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function guest_is_redirected_to_login_when_accessing_recepcion_route(): void
    {
        $response = $this->get('/recepcion/dashboard');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function guest_is_redirected_to_login_when_accessing_caja_route(): void
    {
        $response = $this->get('/caja/dashboard');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function login_with_valid_credentials_succeeds(): void
    {
        $user = User::factory()->admin()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_with_wrong_password_fails(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'contrasena_incorrecta',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    #[Test]
    public function login_with_nonexistent_email_fails(): void
    {
        $response = $this->post('/login', [
            'email' => 'noexiste@clinicaeden.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    #[Test]
    public function login_with_empty_credentials_fails(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function inactive_user_is_blocked_from_role_protected_routes(): void
    {
        $user = User::factory()->admin()->inactivo()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_active_user_is_redirected_away_from_login(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/login');

        // Fortify redirige a los usuarios ya autenticados fuera del login
        $this->assertNotEquals(200, $response->getStatusCode());
    }
}
