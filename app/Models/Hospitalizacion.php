<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo que representa cada proceso de hospitalización.
 *
 * Gestiona asignaciones de personal, costos, estados clínicos y relaciones
 * con tratamientos, facturas y encuestas de satisfacción.
 */
class Hospitalizacion extends Model
{
    use HasFactory;

    /**
     * @var string Nombre de la tabla asociada
     */
    protected $table = 'hospitalizaciones';

    /**
     * @var array<int, string> Atributos asignables en masa
     */
    protected $fillable = [
        'paciente_id',
        'habitacion_id',
        'medico_general_id',
        'jefe_enfermeria_id',
        'auxiliar_enfermeria_id',
        'fecha_ingreso',
        'fecha_egreso',
        'estado',
        'motivo_hospitalizacion',
        'observaciones',
        'observaciones_alta_enfermeria',
        'fecha_alta_enfermeria',
        'comentarios_auxiliares',
        'costo_total',
        'pago_completado',
    ];

    /**
     * @var array<string, string> Conversión de tipos para atributos
     */
    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_egreso' => 'datetime',
        'costo_total' => 'decimal:2',
        'pago_completado' => 'boolean',
    ];

    // Relaciones
    /**
     * Relación: paciente asociado a la hospitalización.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Relación: habitación donde se aloja al paciente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }

    /**
     * Relación: médico general asignado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medicoGeneral()
    {
        return $this->belongsTo(User::class, 'medico_general_id');
    }

    /**
     * Relación: jefe de enfermería responsable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jefeEnfermeria()
    {
        return $this->belongsTo(User::class, 'jefe_enfermeria_id');
    }

    /**
     * Relación: auxiliar de enfermería asignado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function auxiliarEnfermeria()
    {
        return $this->belongsTo(User::class, 'auxiliar_enfermeria_id');
    }

    /**
     * Relación: tratamientos indicados durante la hospitalización.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class);
    }

    /**
     * Relación: facturas asociadas a la hospitalización.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    /**
     * Relación: encuestas de satisfacción vinculadas al alta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function encuestasSatisfaccion()
    {
        return $this->hasMany(EncuestaSatisfaccion::class);
    }

    // Scopes
    /**
     * Scope para hospitalizaciones activas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para filtrar hospitalizaciones por paciente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param int $pacienteId Identificador del paciente
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorPaciente($query, $pacienteId)
    {
        return $query->where('paciente_id', $pacienteId);
    }

    /**
     * Scope para filtrar hospitalizaciones por habitación.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param int $habitacionId Identificador de la habitación
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorHabitacion($query, $habitacionId)
    {
        return $query->where('habitacion_id', $habitacionId);
    }

    // Métodos auxiliares
    /**
     * Validar si la hospitalización puede pasar al proceso de alta.
     *
     * @return bool
     */
    public function puedeDarAlta()
    {
        return in_array($this->estado, ['alta_medica', 'alta_enfermeria']);
    }

    /**
     * Verificar si la hospitalización ya fue completada.
     *
     * @return bool
     */
    public function estaCompleta()
    {
        return $this->estado === 'completado';
    }

    /**
     * Calcular y actualizar el costo total de la hospitalización.
     *
     * @return float Monto acumulado de los tratamientos
     */
    public function calcularCostoTotal()
    {
        $costo = $this->tratamientos()->sum('costo');
        $this->update(['costo_total' => $costo]);
        return $costo;
    }
}
