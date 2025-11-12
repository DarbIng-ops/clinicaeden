<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AutorizacionAltaMedico extends Notification
{
    use Queueable;

    public $paciente;
    public $medico;
    public $observaciones;

    public function __construct($paciente, $medico, $observaciones = null)
    {
        $this->paciente = $paciente;
        $this->medico = $medico;
        $this->observaciones = $observaciones;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'autorizacion_alta',
            'paciente_id' => $this->paciente->id,
            'paciente_nombre' => $this->paciente->nombre_completo,
            'paciente_dni' => $this->paciente->dni,
            'medico_id' => $this->medico->id,
            'medico_nombre' => $this->medico->name,
            'observaciones' => $this->observaciones,
            'mensaje' => "Dr. {$this->medico->name} autorizó el alta de {$this->paciente->nombre_completo}",
            'icono' => 'fa-check-circle',
            'color' => 'success',
            'url' => route('jefe-enfermeria.pacientes.show', $this->paciente->id),
        ];
    }
}