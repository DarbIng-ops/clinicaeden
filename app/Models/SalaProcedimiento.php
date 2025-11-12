<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaProcedimiento extends Model
{
    use HasFactory;

    protected $table = 'salas_procedimientos';

    protected $fillable = [
        'modulo_id',
        'numero',
        'nombre',
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

    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class);
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    public function scopePorModulo($query, $moduloId)
    {
        return $query->where('modulo_id', $moduloId);
    }
}
