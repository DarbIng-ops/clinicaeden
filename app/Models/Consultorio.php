<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'piso_id',
        'nombre',
        'descripcion',
        'disponible',
    ];

    protected $casts = [
        'disponible' => 'boolean',
    ];

    // Relaciones
    public function piso()
    {
        return $this->belongsTo(Piso::class, 'piso_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    public function scopePorPiso($query, $pisoId)
    {
        return $query->where('piso_id', $pisoId);
    }
}
