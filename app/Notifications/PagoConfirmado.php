<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PagoConfirmado extends Notification
{
    use Queueable;

    public $factura;
    public $paciente;
    public $cajero;
    public $monto;
    public $metodoPago;

    public function __construct($factura, $paciente, $cajero, $monto, $metodoPago)
    {
        $this->factura = $factura;
        $this->paciente = $paciente;
        $this->cajero = $cajero;
        $this->monto = $monto;
        $this->metodoPago = $metodoPago;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'pago_confirmado',
            'factura_id' => $this->factura->id ?? null,
            'paciente_id' => $this->paciente->id,
            'paciente_nombre' => $this->paciente->nombre_completo,
            'paciente_dni' => $this->paciente->dni,
            'cajero_id' => $this->cajero->id,
            'cajero_nombre' => $this->cajero->name,
            'monto' => $this->monto,
            'metodo_pago' => $this->metodoPago,
            'mensaje' => "Pago confirmado: {$this->paciente->nombre_completo} - ${$this->monto} ({$this->metodoPago})",
            'icono' => 'fa-money-bill-wave',
            'color' => 'success',
            'url' => route('recepcion.facturas.show', $this->factura->id ?? $this->paciente->id),
        ];
    }
}