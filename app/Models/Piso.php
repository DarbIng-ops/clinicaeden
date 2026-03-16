<?php

/**
 * Piso.php
 *
 * Representa los pisos o plantas del edificio hospitalario.
 *
 * @package PulsoCore
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Piso extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function consultorios()
    {
        return $this->hasMany(Consultorio::class, 'piso_id');
    }

    public function modulosEnfermeria()
    {
        return $this->hasMany(ModuloEnfermeria::class, 'piso_id');
    }

    public function habitaciones()
    {
        return $this->hasManyThrough(Habitacion::class, ModuloEnfermeria::class, 'piso_id', 'modulo_id');
    }

    // Métodos auxiliares
    public function getTotalConsultoriosAttribute()
    {
        return $this->consultorios()->count();
    }

    public function getTotalModulosAttribute()
    {
        return $this->modulosEnfermeria()->count();
    }

    public function getTotalHabitacionesAttribute()
    {
        return $this->habitaciones()->count();
    }

    public function getTotalCamasAttribute()
    {
        return $this->habitaciones()->sum('capacidad');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
