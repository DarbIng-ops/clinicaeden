<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrdenMedicaCreada extends Notification
{
    use Queueable;

    public $consulta;
    public $paciente;
    public $medico;

    public function __construct($consulta, $paciente, $medico)
    {
        $this->consulta = $consulta;
        $this->paciente = $paciente;
        $this->medico = $medico;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'orden_medica',
            'consulta_id' => $this->consulta->id,
            'paciente_id' => $this->paciente->id,
            'paciente_nombre' => $this->paciente->nombre_completo,
            'paciente_dni' => $this->paciente->dni,
            'medico_id' => $this->medico->id,
            'medico_nombre' => $this->medico->name,
            'mensaje' => "Nueva orden médica para {$this->paciente->nombre_completo}",
            'icono' => 'fa-file-medical',
            'color' => 'primary',
            'url' => route('jefe-enfermeria.ordenes.show', $this->consulta->id),
        ];
    }
}