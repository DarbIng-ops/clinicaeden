<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModuloEnfermeria extends Model
{
    use HasFactory;

    protected $table = 'modulos_enfermeria';

    protected $fillable = [
        'piso_id',
        'nombre',
        'tipo',
        'descripcion',
        'jefe_enfermeria_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function piso()
    {
        return $this->belongsTo(Piso::class, 'piso_id');
    }

    public function jefeEnfermeria()
    {
        return $this->belongsTo(User::class, 'jefe_enfermeria_id');
    }

    public function auxiliares()
    {
        return $this->belongsToMany(User::class, 'equipos_enfermeria', 'modulo_id', 'auxiliar_enfermeria_id')
                    ->withPivot('activo')
                    ->withTimestamps();
    }

    public function habitaciones()
    {
        return $this->hasMany(Habitacion::class, 'modulo_id');
    }

    public function salasProcedimientos()
    {
        return $this->hasMany(SalaProcedimiento::class, 'modulo_id');
    }

    public function hospitalizaciones()
    {
        return $this->hasManyThrough(Hospitalizacion::class, Habitacion::class, 'modulo_id', 'habitacion_id');
    }

    // Métodos auxiliares
    public function getTotalAuxiliaresAttribute()
    {
        return $this->auxiliares()->wherePivot('activo', true)->count();
    }

    public function getTotalHabitacionesAttribute()
    {
        return $this->habitaciones()->count();
    }

    public function getTotalCamasAttribute()
    {
        return $this->habitaciones()->sum('capacidad');
    }

    public function getCamasOcupadasAttribute()
    {
        return $this->hospitalizaciones()->where('estado', 'activo')->count();
    }

    public function getCamasDisponiblesAttribute()
    {
        return $this->total_camas - $this->camas_ocupadas;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('modulos_enfermeria.activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorPiso($query, $pisoId)
    {
        return $query->where('piso_id', $pisoId);
    }
}
