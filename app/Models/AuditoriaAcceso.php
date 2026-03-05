<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * AuditoriaAcceso.php
 *
 * Registro de accesos y acciones críticas del sistema.
 * Implementación conforme a ISO 27001 — Control de acceso
 * y trazabilidad de operaciones sobre datos sensibles.
 *
 * @package ClinicaEden
 * @author  Alirio Portilla
 * @version 3.0.0
 */
class AuditoriaAcceso extends Model
{
    /** Los logs son inmutables — sin updated_at */
    const UPDATED_AT = null;

    public $timestamps = false;

    protected $table = 'auditoria_accesos';

    protected $fillable = [
        'user_id',
        'accion',
        'modelo',
        'modelo_id',
        'ip',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper estático ─────────────────────────────────────

    /**
     * Registra una acción de auditoría en el sistema.
     *
     * @param string      $accion    Tipo de acción realizada
     * @param string|null $modelo    Modelo afectado (clase)
     * @param int|null    $modeloId  ID del registro afectado
     */
    public static function registrar(
        string $accion,
        ?string $modelo = null,
        ?int $modeloId = null
    ): void {
        try {
            static::create([
                'user_id'    => auth()->id(),
                'accion'     => $accion,
                'modelo'     => $modelo,
                'modelo_id'  => $modeloId,
                'ip'         => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // La auditoría nunca debe interrumpir el flujo principal
        }
    }
}
