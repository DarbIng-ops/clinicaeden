<?php

namespace App\Livewire\Caja;

use Livewire\Component;
use App\Models\Factura;
use App\Models\Tarifa;
use App\Models\NotificacionSistema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PanelCajero extends Component
{
    /** @var int|null Factura activa seleccionada */
    public ?int $factura_id = null;

    /** @var float Porcentaje de descuento 0-100 */
    public float $descuento_porcentaje = 0;

    /** @var string Justificación del descuento */
    public string $motivo_descuento = '';

    /** @var string Método de pago seleccionado */
    public string $metodo_pago = '';

    // ── Computed: lista de espera ─────────────────────────────

    public function getPacientesPendientesProperty(): Collection
    {
        return Factura::where('estado', 'pendiente')
            ->with([
                'paciente',
                'consulta.medico',
                'consulta.tratamientos',
                'hospitalizacion.habitacion',
                'detalles',
            ])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // ── Computed: factura seleccionada ───────────────────────

    public function getFacturaActivaProperty(): ?Factura
    {
        if (! $this->factura_id) {
            return null;
        }

        return Factura::with([
            'paciente',
            'consulta.medico',
            'consulta.tratamientos',
            'consulta.procedimientos',
            'hospitalizacion.habitacion',
            'detalles.tarifa',
        ])->find($this->factura_id);
    }

    // ── Computed: items de la factura ─────────────────────────

    public function getItemsFacturaProperty(): Collection
    {
        $factura = $this->facturaActiva;

        if (! $factura) {
            return collect();
        }

        // Si ya existen detalles registrados, usarlos directamente
        if ($factura->detalles->isNotEmpty()) {
            return $factura->detalles;
        }

        // Reconstruir desde relaciones (facturas sin detalles)
        $items = collect();

        // 📋 Consulta
        if ($factura->consulta) {
            $tipo = strtolower($factura->consulta->tipo_consulta ?? '');
            $nombreTarifa = match(true) {
                str_contains($tipo, 'especialista') => 'Consulta médica especialista',
                str_contains($tipo, 'urgencia')     => 'Consulta de urgencias',
                str_contains($tipo, 'control')      => 'Control / seguimiento',
                default                              => 'Consulta médica general',
            };
            $tarifa = Tarifa::where('nombre', $nombreTarifa)->first();
            $precio = $tarifa?->precio ?? 0;

            $items->push((object) [
                'tipo'              => 'consulta',
                'concepto'          => $nombreTarifa,
                'cantidad'          => 1,
                'precio_unitario'   => $precio,
                'subtotal'          => $precio,
            ]);
        }

        // 🔬 Procedimientos
        foreach ($factura->consulta?->procedimientos ?? [] as $proc) {
            $tarifa = Tarifa::where('categoria', 'procedimientos')
                ->where('nombre', 'like', '%' . substr($proc->nombre_procedimiento ?? '', 0, 10) . '%')
                ->first();
            $precio = $tarifa?->precio ?? 0;

            $items->push((object) [
                'tipo'              => 'procedimiento',
                'concepto'          => $proc->nombre_procedimiento ?? 'Procedimiento',
                'cantidad'          => 1,
                'precio_unitario'   => $precio,
                'subtotal'          => $precio,
            ]);
        }

        // 💉 Tratamientos (consulta y hospitalización)
        $tratamientos = collect()
            ->merge($factura->consulta?->tratamientos ?? collect())
            ->merge($factura->hospitalizacion?->tratamientos ?? collect());

        foreach ($tratamientos as $trat) {
            $costo = (float) ($trat->costo ?? 0);
            $items->push((object) [
                'tipo'              => 'tratamiento',
                'concepto'          => $trat->descripcion ?? 'Tratamiento',
                'cantidad'          => 1,
                'precio_unitario'   => $costo,
                'subtotal'          => $costo,
            ]);
        }

        // 🛏 Hospitalización
        if ($factura->hospitalizacion) {
            $hosp = $factura->hospitalizacion;
            $fechaFin = $hosp->fecha_egreso ?? now();
            $dias = max(1, (int) $hosp->fecha_ingreso?->diffInDays($fechaFin));
            $costoTotal = (float) ($hosp->costo_total ?? 0);
            $precioDia  = $dias > 0 ? $costoTotal / $dias : $costoTotal;

            $items->push((object) [
                'tipo'              => 'hospitalizacion',
                'concepto'          => 'Hospitalización — ' . ($hosp->habitacion?->nombre ?? 'habitación') . " ({$dias} día(s))",
                'cantidad'          => $dias,
                'precio_unitario'   => $precioDia,
                'subtotal'          => $costoTotal,
            ]);
        }

        return $items;
    }

    // ── Computed: cálculos ────────────────────────────────────

    public function getSubtotalProperty(): float
    {
        // Sumar desde los ítems (detalles reales o reconstruidos) para garantizar exactitud
        return (float) $this->itemsFactura->sum(fn ($item) => $item->subtotal);
    }

    public function getDescuentoMontoProperty(): float
    {
        if ($this->descuento_porcentaje <= 0) {
            return 0;
        }

        return round($this->subtotal * ($this->descuento_porcentaje / 100), 2);
    }

    public function getTotalFinalProperty(): float
    {
        return max(0, $this->subtotal - $this->descuentoMonto);
    }

    // ── Acciones ──────────────────────────────────────────────

    public function seleccionarFactura(int $facturaId): void
    {
        $this->factura_id          = $facturaId;
        $this->descuento_porcentaje = 0;
        $this->motivo_descuento    = '';
        $this->metodo_pago         = '';
    }

    public function cancelarSeleccion(): void
    {
        $this->reset(['factura_id', 'descuento_porcentaje', 'motivo_descuento', 'metodo_pago']);
    }

    public function procesarPago(): void
    {
        // Validación dinámica según si hay descuento
        $rules = [
            'factura_id'            => 'required|exists:facturas,id',
            'metodo_pago'           => 'required|in:efectivo,tarjeta_debito,tarjeta_credito,transferencia,eps_convenio',
            'descuento_porcentaje'  => 'numeric|min:0|max:100',
        ];

        if ($this->descuento_porcentaje > 0) {
            $rules['motivo_descuento'] = 'required|min:5|max:500';
        }

        $this->validate($rules, [
            'metodo_pago.required'           => 'Selecciona un método de pago.',
            'motivo_descuento.required'      => 'El motivo del descuento es obligatorio cuando hay descuento.',
            'motivo_descuento.min'           => 'El motivo debe tener al menos 5 caracteres.',
            'descuento_porcentaje.max'       => 'El descuento no puede superar el 100%.',
        ]);

        $factura = Factura::find($this->factura_id);

        if (! $factura || $factura->estado !== 'pendiente') {
            $this->addError('factura_id', 'La factura no está disponible para pago.');
            return;
        }

        $descuentoMonto = round($factura->subtotal * ($this->descuento_porcentaje / 100), 2);
        $totalFinal     = max(0, $factura->subtotal - $descuentoMonto);

        DB::transaction(function () use ($factura, $descuentoMonto, $totalFinal) {
            $factura->update([
                'estado'                => 'pagado',
                'metodo_pago'           => $this->metodo_pago,
                'total'                 => $totalFinal,
                'descuento_porcentaje'  => $this->descuento_porcentaje,
                'descuento_monto'       => $descuentoMonto,
                'motivo_descuento'      => $this->motivo_descuento ?: null,
                'caja_id'               => auth()->id(),
                'fecha_pago'            => now(),
            ]);

            // PASO B — marcar paciente como pendiente de salida
            $factura->paciente?->update(['estado' => 'pendiente_salida']);

            // Notificaciones (no críticas)
            try {
                // Al cajero
                NotificacionSistema::create([
                    'user_id' => auth()->id(),
                    'titulo'  => 'Pago procesado',
                    'mensaje' => "Factura {$factura->numero_factura} cobrada exitosamente. "
                               . 'Total: $ ' . number_format($totalFinal, 0, ',', '.'),
                    'leida'   => false,
                ]);

                // A recepción: paciente listo para salida
                $nombrePaciente = trim(($factura->paciente?->nombres ?? '') . ' ' . ($factura->paciente?->apellidos ?? ''));
                $recepcionistas = \App\Models\User::where('role', 'recepcionista')->where('activo', 1)->get();
                foreach ($recepcionistas as $rec) {
                    NotificacionSistema::create([
                        'user_id' => $rec->id,
                        'titulo'  => 'Paciente pendiente de salida',
                        'mensaje' => "{$nombrePaciente} completó su pago y está listo para ser dado de alta.",
                        'leida'   => false,
                    ]);
                }
            } catch (\Throwable) {
                // Notificaciones no críticas
            }
        });

        session()->flash('success', "✅ Pago de {$factura->numero_factura} procesado exitosamente.");
        $this->cancelarSeleccion();
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        // .extends() usa @extends/@yield, compatible con layouts/app.blade.php tradicional
        return view('livewire.caja.panel-cajero')
            ->extends('layouts.app')
            ->section('content');
    }
}
