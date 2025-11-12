<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CP-012: Reactivar usuario
 * 
 * Verificar que admin puede reactivar usuarios inactivos.
 */
class ReactivarUsuarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_puede_reactivar_usuario_inactivo()
    {
        // Precondiciones: Usuario inactivo existente
        
        // Crear admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        // Crear usuario inactivo
        $usuario = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 0
        ]);

        // Autenticarse como admin
        $this->actingAs($admin);

        // Paso 1: Ir a Gestión de Usuarios
        // Paso 2: Ver usuarios inactivos
        // Paso 3: Clic en Reactivar
        $response = $this->patch("/admin/usuarios/{$usuario->id}/reactivar");

        // Resultado Esperado: 
        // - Usuario reactivado (activo=1)
        // - Puede hacer login
        
        // Verificar que el usuario está reactivado
        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'activo' => 1
        ]);

        // Verificar que el usuario actualizado tiene activo=1
        $usuarioActualizado = User::find($usuario->id);
        $this->assertTrue($usuarioActualizado->activo);

        // Verificar redirección
        $response->assertRedirect(route('admin.usuarios'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function usuario_reactivado_puede_acceder_a_su_modulo()
    {
        // Crear admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        // Crear usuario inactivo
        $usuario = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 0
        ]);

        $this->actingAs($admin);

        // Verificar que NO puede acceder antes de reactivar
        $usuario->refresh();
        $this->assertFalse($usuario->activo);

        // Reactivar usuario
        $this->patch("/admin/usuarios/{$usuario->id}/reactivar");

        // Recargar el usuario desde la BD para obtener el estado actualizado
        $usuario->refresh();

        // Simular login exitoso
        $this->actingAs($usuario);

        // Intentar acceder a su módulo (ahora debería funcionar)
        $response = $this->get('/medico-general/dashboard');

        // Verificar que puede acceder (no es redirigido)
        $response->assertStatus(200);
    }

    /** @test */
    public function usuario_reactivado_aparece_en_lista_de_activos()
    {
        // Crear admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        // Crear usuario inactivo
        $usuario = User::factory()->create([
            'role' => 'recepcionista',
            'activo' => 0
        ]);

        $this->actingAs($admin);

        // Verificar que está en lista de inactivos antes
        $inactivosAntes = User::where('activo', 0)->get();
        $this->assertTrue($inactivosAntes->contains('id', $usuario->id));

        // Verificar que NO está en lista de activos antes
        $activosAntes = User::activos()->get();
        $this->assertFalse($activosAntes->contains('id', $usuario->id));

        // Reactivar
        $this->patch("/admin/usuarios/{$usuario->id}/reactivar");

        // Verificar que ya NO está en lista de inactivos
        $inactivosDespues = User::where('activo', 0)->get();
        $this->assertFalse($inactivosDespues->contains('id', $usuario->id));

        // Verificar que aparece en lista de activos
        $activosDespues = User::activos()->get();
        $this->assertTrue($activosDespues->contains('id', $usuario->id));
    }

    /** @test */
    public function admin_puede_reactivar_usuarios_de_diferentes_roles()
    {
        // Crear admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $roles = [
            'recepcionista',
            'medico_general',
            'medico_especialista',
            'jefe_enfermeria',
            'auxiliar_enfermeria',
            'caja'
        ];

        $this->actingAs($admin);

        foreach ($roles as $index => $rol) {
            // Crear usuario inactivo con cada rol
            $usuario = User::factory()->create([
                'role' => $rol,
                'activo' => 0
            ]);

            // Reactivar
            $this->patch("/admin/usuarios/{$usuario->id}/reactivar");

            // Verificar reactivación
            $this->assertDatabaseHas('users', [
                'id' => $usuario->id,
                'activo' => 1
            ]);
        }
    }

    /** @test */
    public function no_se_puede_reactivar_usuario_que_no_existe()
    {
        // Crear admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar reactivar un usuario que no existe
        $response = $this->patch("/admin/usuarios/99999/reactivar");

        // Verificar que falla con 404
        $response->assertStatus(404);
    }

    /** @test */
    public function no_se_puede_reactivar_usuario_ya_activo()
    {
        // Crear admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => 1
        ]);

        // Crear usuario activo
        $usuario = User::factory()->create([
            'role' => 'medico_general',
            'activo' => 1
        ]);

        $this->actingAs($admin);

        // Intentar reactivar un usuario que ya está activo
        $response = $this->patch("/admin/usuarios/{$usuario->id}/reactivar");

        // Verificar que se completa correctamente (es idempotente)
        $response->assertRedirect(route('admin.usuarios'));
        $response->assertSessionHas('success');

        // Verificar que sigue activo
        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'activo' => 1
        ]);
    }
}

