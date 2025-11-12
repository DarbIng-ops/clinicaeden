<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevaInterconsulta extends Notification
{
    use Queueable;

    public $paciente;
    public $medico;
    public $especialidad;
    public $mensaje;
    public $urgente;

    public function __construct($paciente, $medico, $especialidad, $mensaje = null, $urgente = false)
    {
        $this->paciente = $paciente;
        $this->medico = $medico;
        $this->especialidad = $especialidad;
        $this->mensaje = $mensaje;
        $this->urgente = $urgente;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'interconsulta',
            'urgente' => $this->urgente,
            'paciente_id' => $this->paciente->id,
            'paciente_nombre' => $this->paciente->nombre_completo,
            'paciente_dni' => $this->paciente->dni,
            'medico_id' => $this->medico->id,
            'medico_nombre' => $this->medico->name,
            'especialidad' => $this->especialidad,
            'mensaje' => $this->mensaje ?? "Nueva interconsulta de {$this->especialidad}",
            'icono' => 'fa-stethoscope',
            'color' => $this->urgente ? 'danger' : 'info',
            'url' => route('medico.interconsultas.show', $this->paciente->id),
        ];
    }
}