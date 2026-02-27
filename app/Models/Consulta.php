<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo que representa las consultas médicas realizadas en la clínica.
 *
 * Contiene datos clínicos, signos vitales y vínculos con tratamientos,
 * procedimientos, encuestas de satisfacción y la historia clínica correspondiente.
 */
class Consulta extends Model
{
    use HasFactory;

    /**
     * @var array<int, string> Atributos asignables en masa
     */
    protected $fillable = [
        'historia_clinica_id',
        'cita_id',
        'medico_id',
        'paciente_id',
        'fecha_consulta',
        'motivo',
        'motivo_consulta',
        'hora_consulta',
        'sintomas',
        'diagnostico',
        'tratamiento',
        'tipo_consulta',
        'especialidad',
        'estado',
        'observaciones',
        'presion_arterial',
        'temperatura',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'saturacion_oxigeno',
        'peso',
        'talla',
        'hora_atencion',
    ];

    /**
     * @var array<string, string> Conversión de tipos para atributos
     */
    protected $casts = [
        'fecha_consulta' => 'datetime',
        'hora_atencion' => 'datetime',
        'temperatura' => 'decimal:2',
        'peso' => 'decimal:2',
        'talla' => 'decimal:2',
    ];

    // Relaciones
    /**
     * Relación: historia clínica asociada a la consulta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    /**
     * Relación: cita de origen de la consulta (si aplica).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    /**
     * Relación: médico que atiende la consulta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    /**
     * Relación: paciente atendido durante la consulta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Relación: tratamientos derivados de la consulta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class);
    }

    /**
     * Relación: fórmulas médicas entregadas durante la consulta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formulasMedicas()
    {
        return $this->hasMany(FormulaMedica::class);
    }

    /**
     * Relación: procedimientos realizados asociados a la consulta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class);
    }

    /**
     * Relación: encuesta de satisfacción vinculada a la consulta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function encuestaSatisfaccion()
    {
        return $this->hasOne(EncuestaSatisfaccion::class, 'consulta_id');
    }

    /**
     * Relación: factura emitida para esta consulta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function factura()
    {
        return $this->hasOne(Factura::class, 'consulta_id');
    }
}