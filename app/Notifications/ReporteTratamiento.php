<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReporteTratamiento extends Notification
{
    use Queueable;

    public $tratamiento;
    public $paciente;
    public $auxiliar;
    public $estado;
    public $observaciones;

    public function __construct($tratamiento, $paciente, $auxiliar, $estado, $observaciones = null)
    {
        $this->tratamiento = $tratamiento;
        $this->paciente = $paciente;
        $this->auxiliar = $auxiliar;
        $this->estado = $estado;
        $this->observaciones = $observaciones;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'reporte_tratamiento',
            'tratamiento_id' => $this->tratamiento->id,
            'paciente_id' => $this->paciente->id,
            'paciente_nombre' => $this->paciente->nombre_completo,
            'auxiliar_id' => $this->auxiliar->id,
            'auxiliar_nombre' => $this->auxiliar->name,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'mensaje' => "Reporte de tratamiento: {$this->paciente->nombre_completo} - Estado: {$this->estado}",
            'icono' => 'fa-clipboard-check',
            'color' => 'info',
            'url' => route('jefe-enfermeria.tratamientos.show', $this->tratamiento->id),
        ];
    }
}