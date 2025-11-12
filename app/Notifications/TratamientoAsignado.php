<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TratamientoAsignado extends Notification
{
    use Queueable;

    public $tratamiento;
    public $paciente;
    public $jefe;
    public $prioridad;

    public function __construct($tratamiento, $paciente, $jefe, $prioridad = 'normal')
    {
        $this->tratamiento = $tratamiento;
        $this->paciente = $paciente;
        $this->jefe = $jefe;
        $this->prioridad = $prioridad;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'tratamiento_asignado',
            'tratamiento_id' => $this->tratamiento->id,
            'paciente_id' => $this->paciente->id,
            'paciente_nombre' => $this->paciente->nombre_completo,
            'paciente_dni' => $this->paciente->dni,
            'jefe_id' => $this->jefe->id,
            'jefe_nombre' => $this->jefe->name,
            'prioridad' => $this->prioridad,
            'mensaje' => "Nuevo tratamiento asignado: {$this->paciente->nombre_completo}",
            'icono' => 'fa-tasks',
            'color' => $this->prioridad === 'urgente' ? 'danger' : 'warning',
            'url' => route('auxiliar.tratamientos.show', $this->tratamiento->id),
        ];
    }
}