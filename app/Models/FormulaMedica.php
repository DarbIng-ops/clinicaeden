<?php

/**
 * FormulaMedica.php
 *
 * Representa las fórmulas médicas (recetas) emitidas durante una consulta.
 *
 * @package ClinicaEden
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormulaMedica extends Model
{
    use HasFactory;

    protected $fillable = [
        'consulta_id',
        'medicamento',
        'dosis',
        'frecuencia',
        'duracion',
        'indicaciones',
    ];

    // Relaciones
    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }
}