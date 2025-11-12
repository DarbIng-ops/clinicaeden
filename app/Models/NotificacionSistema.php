<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificacionSistema extends Model
{
    use HasFactory;

    protected $table = 'notificaciones_sistema';

    protected $fillable = [
        'usuario_emisor_id',
        'usuario_receptor_id',
        'tipo',
        'titulo',
        'mensaje',
        'datos_adicionales',
        'leida',
        'fecha_leida',
    ];

    protected $casts = [
        'datos_adicionales' => 'array',
        'leida' => 'boolean',
        'fecha_leida' => 'datetime',
    ];

    // Relaciones
    public function usuarioEmisor()
    {
        return $this->belongsTo(User::class, 'usuario_emisor_id');
    }

    public function usuarioReceptor()
    {
        return $this->belongsTo(User::class, 'usuario_receptor_id');
    }

    // Scopes
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }

    public function scopePorReceptor($query, $usuarioId)
    {
        return $query->where('usuario_receptor_id', $usuarioId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Métodos auxiliares
    public function marcarComoLeida()
    {
        $this->update([
            'leida' => true,
            'fecha_leida' => now()
        ]);
    }

    public static function crearNotificacion($emisorId, $receptorId, $tipo, $titulo, $mensaje, $datosAdicionales = null)
    {
        return self::create([
            'usuario_emisor_id' => $emisorId,
            'usuario_receptor_id' => $receptorId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'datos_adicionales' => $datosAdicionales,
        ]);
    }
}
