<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitudGeneral extends Notification
{
    use Queueable;

    public $usuario;
    public $asunto;
    public $mensaje;
    public $tipo;

    public function __construct($usuario, $asunto, $mensaje, $tipo = 'general')
    {
        $this->usuario = $usuario;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->tipo = $tipo; // general, soporte, urgente
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'solicitud_admin',
            'usuario_id' => $this->usuario->id,
            'usuario_nombre' => $this->usuario->name,
            'usuario_rol' => $this->usuario->role,
            'asunto' => $this->asunto,
            'mensaje' => $this->mensaje,
            'tipo_solicitud' => $this->tipo,
            'icono' => $this->tipo === 'urgente' ? 'fa-exclamation-circle' : 'fa-envelope',
            'color' => $this->tipo === 'urgente' ? 'danger' : 'warning',
            'url' => route('admin.solicitudes.show', $this->usuario->id),
        ];
    }
}