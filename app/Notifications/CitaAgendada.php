<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CitaAgendada extends Notification
{
    use Queueable;

    public $cita;

    public function __construct($cita)
    {
        $this->cita = $cita;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'cita_agendada',
            'cita_id' => $this->cita->id,
            'paciente_id' => $this->cita->paciente_id,
            'paciente_nombre' => $this->cita->paciente->nombre_completo,
            'paciente_dni' => $this->cita->paciente->dni,
            'recepcionista_id' => $this->cita->recepcionista_id,
            'recepcionista_nombre' => $this->cita->recepcionista->name ?? 'Sistema',
            'fecha' => $this->cita->fecha->format('d/m/Y'),
            'hora' => $this->cita->hora->format('H:i'),
            'motivo' => $this->cita->motivo_consulta,
            'mensaje' => "Nueva cita: {$this->cita->paciente->nombre_completo} - {$this->cita->fecha->format('d/m/Y')} {$this->cita->hora->format('H:i')}",
            'icono' => 'fa-calendar-plus',
            'color' => 'primary',
            'url' => route('medico.citas.show', $this->cita->id),
        ];
    }
}