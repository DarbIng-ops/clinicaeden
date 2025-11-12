<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipoEnfermeria extends Model
{
    use HasFactory;

    protected $table = 'equipos_enfermeria';

    protected $fillable = [
        'jefe_enfermeria_id',
        'auxiliar_enfermeria_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function jefeEnfermeria()
    {
        return $this->belongsTo(User::class, 'jefe_enfermeria_id');
    }

    public function auxiliarEnfermeria()
    {
        return $this->belongsTo(User::class, 'auxiliar_enfermeria_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorJefe($query, $jefeId)
    {
        return $query->where('jefe_enfermeria_id', $jefeId);
    }

    public function scopePorAuxiliar($query, $auxiliarId)
    {
        return $query->where('auxiliar_enfermeria_id', $auxiliarId);
    }
}
