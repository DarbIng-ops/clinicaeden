<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * Middleware para validar roles de usuario en rutas protegidas.
 *
 * Verifica autenticación, estado activo y correspondencia de roles requeridos
 * antes de permitir el acceso a un recurso específico.
 */
class RoleMiddleware
{
    /**
     * Roles válidos en el sistema
     */
    private const VALID_ROLES = [
        'admin',
        'medico_general',
        'medico_especialista',
        'recepcionista',
        'auxiliar_enfermeria',
        'jefe_enfermeria',
        'caja'
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request Petición HTTP entrante
     * @param \Closure $next Siguiente middleware o controlador
     * @param string ...$roles Roles permitidos para la ruta
     * @return \Symfony\Component\HttpFoundation\Response Respuesta resultante del pipeline
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar si el usuario está autenticado
        if (!$request->user()) {
            Log::warning('Intento de acceso sin autenticación', [
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_agent' => $request->userAgent()
            ]);
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }

        $user = $request->user();
        
        // Verificar que el usuario esté activo
        if (!$user->activo) {
            Log::warning('Intento de acceso de usuario inactivo', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'url' => $request->url()
            ]);
            return redirect()->route('login')->with('error', 'Tu cuenta está inactiva. Contacta al administrador.');
        }

        // Verificar que el rol del usuario sea válido
        if (!in_array($user->role, self::VALID_ROLES)) {
            Log::error('Usuario con rol inválido intentó acceder', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip(),
                'url' => $request->url()
            ]);
            return redirect()->route('dashboard')->with('error', 'Tu rol de usuario no es válido. Contacta al administrador.');
        }

        // Verificar que el usuario tenga uno de los roles permitidos
        if (!in_array($user->role, $roles)) {
            Log::warning('Intento de acceso sin permisos', [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_role' => $user->role,
                'required_roles' => $roles,
                'ip' => $request->ip(),
                'url' => $request->url()
            ]);
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        // Log de acceso exitoso (solo para operaciones sensibles)
        if (in_array($user->role, ['admin', 'medico_general', 'medico_especialista']) && $request->isMethod('POST')) {
            Log::info('Acceso autorizado a operación sensible', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'method' => $request->method(),
                'url' => $request->url(),
                'ip' => $request->ip()
            ]);
        }

        return $next($request);
    }
}