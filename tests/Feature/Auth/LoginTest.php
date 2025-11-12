<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_puede_hacer_login_con_credenciales_correctas()
    {
        // Crear usuario de prueba
        $user = User::factory()->create([
            'email' => 'test@clinicaeden.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'activo' => 1
        ]);

        // Intentar hacer login
        $response = $this->post('/login', [
            'email' => 'test@clinicaeden.com',
            'password' => 'password123'
        ]);

        // Verificar que funcionó
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

      /** @test */
      public function usuario_no_puede_hacer_login_con_password_incorrecta()
      {
          // Crear usuario
          $user = User::factory()->create([
              'email' => 'test@clinicaeden.com',
              'password' => bcrypt('password123'),
              'activo' => 1
          ]);
  
          // Intentar login con password incorrecta
          $response = $this->post('/login', [
              'email' => 'test@clinicaeden.com',
              'password' => 'wrongpassword'
          ]);
  
          // Verificar que NO se autenticó
          $response->assertSessionHasErrors();
          $this->assertGuest();
      }



}