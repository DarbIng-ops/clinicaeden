<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tratamiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'consulta_id',
        'hospitalizacion_id',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'indicaciones',
        'costo',
        'fecha_programada',
        'fecha_completado',
        'observaciones_procedimiento',
        'hora_aplicacion',
        'completado_por',
        'comentarios_paciente',
        'observaciones_jefe_enfermeria',
        'revisado_por',
        'fecha_revision',
        'dosis',
        'frecuencia',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_programada' => 'date',
        'fecha_completado' => 'datetime',
        'fecha_revision' => 'datetime',
        'hora_aplicacion' => 'datetime:H:i',
        'costo' => 'decimal:2',
    ];

    // Relaciones
    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }

    public function hospitalizacion()
    {
        return $this->belongsTo(Hospitalizacion::class);
    }

    public function completadoPor()
    {
        return $this->belongsTo(User::class, 'completado_por');
    }

    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}