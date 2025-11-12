<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones';

    protected $fillable = [
        'numero',
        'modulo_id',
        'capacidad',
        'tipo',
        'descripcion',
        'disponible',
    ];

    protected $casts = [
        'disponible' => 'boolean',
    ];

    // Relaciones
    public function modulo()
    {
        return $this->belongsTo(ModuloEnfermeria::class, 'modulo_id');
    }

    public function hospitalizaciones()
    {
        return $this->hasMany(Hospitalizacion::class);
    }

    public function hospitalizacionesActivas()
    {
        return $this->hasMany(Hospitalizacion::class)->where('estado', 'activo');
    }

    // Métodos auxiliares
    public function getCapacidadOcupadaAttribute()
    {
        return $this->hospitalizacionesActivas()->count();
    }

    public function getCapacidadDisponibleAttribute()
    {
        return $this->capacidad - $this->capacidad_ocupada;
    }

    public function tieneCapacidadDisponible()
    {
        return $this->capacidad_disponible > 0;
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorModulo($query, $moduloId)
    {
        return $query->where('modulo_id', $moduloId);
    }

    public function scopeConCapacidadDisponible($query)
    {
        return $query->whereHas('hospitalizacionesActivas', function($q) {
            $q->havingRaw('COUNT(*) < habitaciones.capacidad');
        });
    }
}
