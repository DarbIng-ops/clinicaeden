<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EncuestaSatisfaccion extends Model
{
    use HasFactory;

    protected $table = 'encuestas_satisfaccion';

    protected $fillable = [
        'paciente_id',
        'hospitalizacion_id',
        'consulta_id',
        'recepcion_id',
        'atencion_medica',
        'atencion_enfermeria',
        'limpieza_habitacion',
        'comida',
        'personal_recepcion',
        'tiempo_espera',
        'calidad_general',
        'comentarios',
        'recomendaria',
        'fecha_encuesta'
    ];

    protected $casts = [
        'recomendaria' => 'boolean',
        'fecha_encuesta' => 'datetime',
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function hospitalizacion()
    {
        return $this->belongsTo(Hospitalizacion::class);
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }

    public function recepcion()
    {
        return $this->belongsTo(User::class, 'recepcion_id');
    }

    // Métodos auxiliares
    public function calcularPromedioGeneral()
    {
        $puntuaciones = collect([
            $this->atencion_medica,
            $this->atencion_enfermeria,
            $this->limpieza_habitacion,
            $this->comida,
            $this->personal_recepcion,
            $this->tiempo_espera,
            $this->calidad_general,
        ])->filter()->values();

        return $puntuaciones->isNotEmpty() ? $puntuaciones->avg() : 0;
    }

    public function getNivelSatisfaccionAttribute()
    {
        $promedio = $this->calcularPromedioGeneral();
        
        if ($promedio >= 4.5) return 'Excelente';
        if ($promedio >= 3.5) return 'Bueno';
        if ($promedio >= 2.5) return 'Regular';
        if ($promedio >= 1.5) return 'Malo';
        return 'Muy Malo';
    }

    // Scopes
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_encuesta', $fecha);
    }

    public function scopeExcelentes($query)
    {
        return $query->where('calidad_general', '>=', 4.5);
    }

    public function scopeRecomendarian($query)
    {
        return $query->where('recomendaria', true);
    }
}
