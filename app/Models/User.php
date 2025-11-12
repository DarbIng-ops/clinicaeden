<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo principal para los usuarios del sistema.
 *
 * Gestiona credenciales, roles y relaciones con módulos operativos como
 * hospitalizaciones, facturas, encuestas y notificaciones.
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * @var array<int, string> Atributos asignables en masa
     */
    protected $fillable = [
        'name',
        'apellido',
        'dni',
        'email',
        'password',
        'role',
        'diploma_path',
        'especialidad',
        'numero_licencia',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'sexo',
        'observaciones',
        'activo',
    ];

    /**
     * @var array<int, string> Atributos ocultos al serializar
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * @var array<int, string> Atributos calculados a incluir automáticamente
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Obtener las conversiones de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
            'activo' => 'boolean',
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Relación: citas atendidas como médico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function citasComoMedico()
    {
        return $this->hasMany(Cita::class, 'medico_id');
    }

    /**
     * Relación: citas agendadas como recepcionista.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function citasComoRecepcionista()
    {
        return $this->hasMany(Cita::class, 'recepcionista_id');
    }

    /**
     * Relación: consultas médicas realizadas por el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'medico_id');
    }

    // ==========================================
    // RELACIONES PARA EL SISTEMA HOSPITALARIO
    // ==========================================

    /**
     * Relación: hospitalizaciones donde actúa como médico general.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hospitalizacionesComoMedicoGeneral()
    {
        return $this->hasMany(Hospitalizacion::class, 'medico_general_id');
    }

    /**
     * Relación: hospitalizaciones bajo su supervisión como jefe de enfermería.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hospitalizacionesComoJefeEnfermeria()
    {
        return $this->hasMany(Hospitalizacion::class, 'jefe_enfermeria_id');
    }

    /**
     * Relación: hospitalizaciones asignadas como auxiliar de enfermería.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hospitalizacionesComoAuxiliar()
    {
        return $this->hasMany(Hospitalizacion::class, 'auxiliar_enfermeria_id');
    }

    /**
     * Relación: facturas procesadas por el usuario en caja.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facturasComoCaja()
    {
        return $this->hasMany(Factura::class, 'caja_id');
    }

    /**
     * Relación: encuestas de satisfacción registradas desde recepción.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function encuestasSatisfaccionComoRecepcion()
    {
        return $this->hasMany(EncuestaSatisfaccion::class, 'recepcion_id');
    }

    /**
     * Relación: notificaciones emitidas por el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificacionesEnviadas()
    {
        return $this->hasMany(NotificacionSistema::class, 'usuario_emisor_id');
    }

    /**
     * Relación: notificaciones recibidas por el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificacionesRecibidas()
    {
        return $this->hasMany(NotificacionSistema::class, 'usuario_receptor_id');
    }

    /**
     * Relación: notificaciones aún no leídas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificacionesNoLeidas()
    {
        return $this->notificacionesRecibidas()->where('leida', false);
    }

    // Relaciones para equipos de enfermería
    /**
     * Relación: módulos de enfermería donde es jefe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modulosComoJefe()
    {
        return $this->hasMany(ModuloEnfermeria::class, 'jefe_enfermeria_id');
    }

    /**
     * Relación: módulos de enfermería donde participa como auxiliar.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modulosComoAuxiliar()
    {
        return $this->belongsToMany(ModuloEnfermeria::class, 'equipos_enfermeria', 'auxiliar_enfermeria_id', 'modulo_id')
                    ->withPivot('activo')
                    ->withTimestamps()
                    ->wherePivot('equipos_enfermeria.activo', true)
                    ->where('modulos_enfermeria.activo', true);
    }

    /**
     * Relación: equipos de enfermería donde participa como auxiliar.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function equiposEnfermeria()
    {
        return $this->hasMany(EquipoEnfermeria::class, 'auxiliar_enfermeria_id');
    }

    // ==========================================
    // MÉTODOS AUXILIARES PARA ROLES
    // ==========================================

    /**
     * Determinar si el usuario es administrador.
     *
     * @return bool
     */
    public function esAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Determinar si el usuario pertenece a algún rol médico.
     *
     * @return bool
     */
    public function esMedico()
    {
        return in_array($this->role, ['medico_general', 'medico_especialista']);
    }

    /**
     * Determinar si el usuario es médico general.
     *
     * @return bool
     */
    public function esMedicoGeneral()
    {
        return $this->role === 'medico_general';
    }

    /**
     * Determinar si el usuario es médico especialista.
     *
     * @return bool
     */
    public function esMedicoEspecialista()
    {
        return $this->role === 'medico_especialista';
    }

    /**
     * Determinar si el usuario trabaja en recepción.
     *
     * @return bool
     */
    public function esRecepcionista()
    {
        return in_array($this->role, ['recepcionista', 'recepcion']);
    }

    /**
     * Determinar si el usuario pertenece al rol de caja.
     *
     * @return bool
     */
    public function esCaja()
    {
        return $this->role === 'caja';
    }

    /**
     * Determinar si el usuario es jefe de enfermería.
     *
     * @return bool
     */
    public function esJefeEnfermeria()
    {
        return $this->role === 'jefe_enfermeria';
    }

    /**
     * Determinar si el usuario es auxiliar de enfermería.
     *
     * @return bool
     */
    public function esAuxiliarEnfermeria()
    {
        return $this->role === 'auxiliar_enfermeria';
    }

    // ==========================================
    // MÉTODOS AUXILIARES PARA PERMISOS
    // ==========================================

    /**
     * Verificar si el usuario puede gestionar pacientes.
     *
     * @return bool
     */
    public function puedeGestionarPacientes()
    {
        return in_array($this->role, ['admin', 'recepcionista', 'recepcion', 'medico_general', 'medico_especialista']);
    }

    /**
     * Verificar si el usuario puede gestionar tratamientos.
     *
     * @return bool
     */
    public function puedeGestionarTratamientos()
    {
        return in_array($this->role, ['admin', 'medico_general', 'medico_especialista', 'jefe_enfermeria', 'auxiliar_enfermeria']);
    }

    /**
     * Verificar si el usuario puede gestionar facturas.
     *
     * @return bool
     */
    public function puedeGestionarFacturas()
    {
        return in_array($this->role, ['admin', 'caja', 'recepcionista', 'recepcion']);
    }

    /**
     * Verificar si el usuario puede consultar reportes.
     *
     * @return bool
     */
    public function puedeVerReportes()
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario puede administrar otros usuarios.
     *
     * @return bool
     */
    public function puedeGestionarUsuarios()
    {
        return $this->role === 'admin';
    }

    // ==========================================
    // MÉTODOS PARA NOTIFICACIONES
    // ==========================================

    /**
     * Enviar notificación a un usuario específico
     *
     * @param \App\Models\User $usuario Destinatario de la notificación
     * @param \Illuminate\Notifications\Notification $notificacion Notificación a enviar
     * @return void
     */
    public function enviarNotificacionA($usuario, $notificacion)
    {
        $usuario->notify($notificacion);
    }

    /**
     * Enviar notificación a múltiples usuarios por rol
     *
     * @param string $rol Rol objetivo
     * @param \Illuminate\Notifications\Notification $notificacion Notificación a enviar
     * @return void
     */
    public static function notificarPorRol($rol, $notificacion)
    {
        $usuarios = self::where('role', $rol)->where('activo', true)->get();
        
        foreach ($usuarios as $usuario) {
            $usuario->notify($notificacion);
        }
    }

    /**
     * Enviar notificación a todos los médicos con una especialidad específica
     *
     * @param string $especialidad Especialidad objetivo
     * @param \Illuminate\Notifications\Notification $notificacion Notificación a enviar
     * @return void
     */
    public static function notificarEspecialistas($especialidad, $notificacion)
    {
        $especialistas = self::where('role', 'like', '%medico%')
                            ->where('especialidad', $especialidad)
                            ->where('activo', true)
                            ->get();
        
        foreach ($especialistas as $especialista) {
            $especialista->notify($notificacion);
        }
    }

    /**
     * Obtener notificaciones no leídas agrupadas por tipo
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNotificacionesPorTipo()
    {
        return $this->unreadNotifications->groupBy(function($notification) {
            return $notification->data['tipo'] ?? 'general';
        });
    }

    // ==========================================
    // MÉTODOS AUXILIARES PARA DATOS PERSONALES
    // ==========================================

    /**
     * Obtener nombre completo del usuario
     *
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        return trim($this->name . ' ' . $this->apellido);
    }

    /**
     * Obtener edad del usuario
     *
     * @return int|null
     */
    public function getEdadAttribute()
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }
        return $this->fecha_nacimiento->age;
    }

    /**
     * Verificar si el usuario tiene diploma subido
     *
     * @return bool
     */
    public function tieneDiploma()
    {
        return !empty($this->diploma_path);
    }

    /**
     * Obtener URL del diploma
     *
     * @return string|null
     */
    public function getDiplomaUrlAttribute()
    {
        if (!$this->diploma_path) {
            return null;
        }
        return asset('storage/' . $this->diploma_path);
    }

    /**
     * Scope para usuarios activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para usuarios por rol
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param string $rol Rol a filtrar
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorRol($query, $rol)
    {
        return $query->where('role', $rol);
    }

    /**
     * Scope para médicos con especialidad
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param string|null $especialidad Especialidad a filtrar (opcional)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMedicosConEspecialidad($query, $especialidad = null)
    {
        $query = $query->whereIn('role', ['medico_general', 'medico_especialista']);
        
        if ($especialidad) {
            $query->where('especialidad', $especialidad);
        }
        
        return $query;
    }
}