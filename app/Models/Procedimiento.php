<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Procedimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'consulta_id',
        'nombre_procedimiento',
        'descripcion',
        'fecha_realizado',
        'resultado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_realizado' => 'datetime',
    ];

    // Relaciones
    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }
}