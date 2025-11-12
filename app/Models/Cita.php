<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'recepcionista_id',
        'fecha',
        'hora',
        'motivo_consulta',
        'estado',
        'observaciones',
        'cancelada_por',
        'motivo_cancelacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime:H:i',
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function recepcionista()
    {
        return $this->belongsTo(User::class, 'recepcionista_id');
    }

    public function canceladaPor()
    {
        return $this->belongsTo(User::class, 'cancelada_por');
    }

    public function consulta()
    {
        return $this->hasOne(Consulta::class);
    }

    // Scopes para filtrar
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today());
    }
}