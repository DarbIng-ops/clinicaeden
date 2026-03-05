<?php

namespace App\Observers;

use App\Models\AuditoriaAcceso;
use App\Models\Consulta;
use App\Models\Factura;
use App\Models\Paciente;

/**
 * AuditoriaObserver
 *
 * Observer unificado para los modelos críticos del sistema.
 * Registra accesos y modificaciones conforme a ISO 27001.
 *
 * Asociado a: Paciente, Consulta, Factura
 */
class AuditoriaObserver
{
    /**
     * Registra cuando se carga un modelo desde la BD.
     * - Paciente  → 'ver_paciente'
     * - Consulta  → 'ver_historial'
     */
    public function retrieved(mixed $model): void
    {
        if (! auth()->check()) {
            return;
        }

        $accion = match(true) {
            $model instanceof Paciente => 'ver_paciente',
            $model instanceof Consulta => 'ver_historial',
            default                    => null,
        };

        if ($accion) {
            AuditoriaAcceso::registrar($accion, class_basename($model), $model->id);
        }
    }

    /**
     * Registra cuando se crea un modelo nuevo.
     * - Factura → 'generar_factura'
     */
    public function created(mixed $model): void
    {
        if (! auth()->check()) {
            return;
        }

        if ($model instanceof Factura) {
            AuditoriaAcceso::registrar('generar_factura', 'Factura', $model->id);
        }
    }

    /**
     * Registra cuando se actualiza un modelo.
     * - Paciente → 'editar_paciente'
     */
    public function updated(mixed $model): void
    {
        if (! auth()->check()) {
            return;
        }

        if ($model instanceof Paciente) {
            AuditoriaAcceso::registrar('editar_paciente', 'Paciente', $model->id);
        }
    }
}
