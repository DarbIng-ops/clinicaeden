<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MensajeAdmin extends Notification
{
    use Queueable;

    public $titulo;
    public $mensaje;
    public $tipo;
    public $url;

    public function __construct($titulo, $mensaje, $tipo = 'info', $url = null)
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
        $this->tipo = $tipo; // info, warning, success, danger
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $iconos = [
            'info' => 'fa-info-circle',
            'warning' => 'fa-exclamation-triangle',
            'success' => 'fa-check-circle',
            'danger' => 'fa-exclamation-circle',
        ];

        return [
            'tipo' => 'mensaje_admin',
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'tipo_mensaje' => $this->tipo,
            'icono' => $iconos[$this->tipo] ?? 'fa-bell',
            'color' => $this->tipo,
            'url' => $this->url ?? '#',
        ];
    }
}