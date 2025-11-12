<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoriaClinica extends Model
{
    use HasFactory;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'paciente_id',
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }
}