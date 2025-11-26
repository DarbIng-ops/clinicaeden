<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Paciente;
use App\Models\Hospitalizacion;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    /**
     * Mostrar el dashboard principal del área de caja.
     *
     * Presenta el listado de facturas pendientes junto con métricas de ingresos
     * diarios y mensuales para facilitar la gestión financiera.
     *
     * @return \Illuminate\Contracts\View\View Vista con estadísticas y facturas pendientes
     */
    public function index()
    {
        // Facturas pendientes de pago
        $facturasPendientes = Factura::where('estado', 'pendiente')
            ->with(['paciente', 'hospitalizacion'])
            ->orderBy('fecha_emision', 'desc')
            ->paginate(15);

        // Facturas pagadas hoy
        $facturasPagadasHoy = Factura::where('estado', 'pagado')
            ->whereDate('fecha_pago', today())
            ->count();

        // Ingresos del día
        $ingresosHoy = Factura::where('estado', 'pagado')
            ->whereDate('fecha_pago', today())
            ->sum('total');

        // Ingresos del mes
        $ingresosMes = Factura::where('estado', 'pagado')
            ->whereMonth('fecha_pago', now()->month)
            ->whereYear('fecha_pago', now()->year)
            ->sum('total');

        // Estadísticas generales
        $totalFacturasPendientes = Factura::where('estado', 'pendiente')->count();
        $montoPendiente = Factura::where('estado', 'pendiente')->sum('total');

        return view('caja.dashboard', compact(
            'facturasPendientes',
            'facturasPagadasHoy',
            'ingresosHoy',
            'ingresosMes',
            'totalFacturasPendientes',
            'montoPendiente'
        ));
    }

    /**
     * Procesar el pago de una factura determinada.
     *
     * Valida la información del cobro, actualiza el estado de la factura,
     * registra el pago y coordina la siguiente acción con hospitalización o recepción.
     *
     * @param \Illuminate\Http\Request $request Datos de pago ingresados por el cajero
     * @param \App\Models\Factura $factura Factura que se está cobrando
     * @return \Illuminate\Http\RedirectResponse Redirección al dashboard con mensaje de confirmación
     */
    public function procesarPago(Request $request, Factura $factura)
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto_recibido' => 'required|numeric|min:' . $factura->total . '|max:' . ($factura->total * 1.5),
            'observaciones' => 'nullable|string|max:500'
        ]);

        try {
            // Actualizar factura
            $factura->update([
                'estado' => 'pagado',
                'metodo_pago' => $request->metodo_pago,
                'monto_recibido' => $request->monto_recibido,
                'fecha_pago' => now(),
                'caja_id' => Auth::id(),
                'observaciones_pago' => $request->observaciones
            ]);

            // Si la factura está relacionada con una hospitalización, cambiar estado
            if ($factura->hospitalizacion) {
                $hospitalizacion = $factura->hospitalizacion;

                // Solo cambiar a 'alta_pago' si ya tiene AMBAS altas (médica Y enfermería)
                if ($hospitalizacion->fecha_alta_medica && $hospitalizacion->fecha_alta_enfermeria) {
                    $hospitalizacion->update(['estado' => 'alta_pago']);

                    // Notificar a recepción para procesar la salida
                    $this->notificarRecepcion($hospitalizacion);
                }
            }

            Log::info('Pago procesado por caja', [
                'caja_id' => Auth::id(),
                'factura_id' => $factura->id,
                'paciente_id' => $factura->paciente_id,
                'monto' => $factura->total,
                'metodo_pago' => $request->metodo_pago
            ]);

            return redirect()->route('caja.dashboard')
                ->with('success', 'Pago procesado exitosamente. Se ha notificado a recepción.');

        } catch (\Exception $e) {
            Log::error('Error al procesar pago: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al procesar el pago. Inténtalo de nuevo.']);
        }
    }

    /**
     * Mostrar los detalles completos de una factura.
     *
     * @param \App\Models\Factura $factura Factura que se desea visualizar
     * @return \Illuminate\Contracts\View\View Vista con los datos de la factura
     */
    public function verFactura(Factura $factura)
    {
        $factura->load(['paciente', 'hospitalizacion', 'consulta']);
        return view('caja.facturas.ver', compact('factura'));
    }

    /**
     * Renderizar la vista de impresión de una factura.
     *
     * @param \App\Models\Factura $factura Factura que se imprimirá
     * @return \Illuminate\Contracts\View\View Vista optimizada para impresión
     */
    public function imprimirFactura(Factura $factura)
    {
        $factura->load(['paciente', 'hospitalizacion', 'consulta']);
        return view('caja.facturas.imprimir', compact('factura'));
    }

    /**
     * Mostrar el formulario para procesar el pago de una factura.
     *
     * @param \App\Models\Factura $factura Factura seleccionada para el cobro
     * @return \Illuminate\Contracts\View\View Vista con los datos de la factura y formulario de pago
     */
    public function mostrarFormularioPago(Factura $factura)
    {
        $factura->load(['paciente', 'consulta']);
        return view('caja.facturas.procesar-pago', compact('factura'));
    }

    /**
     * Confirmar y registrar el pago de una factura (flujo alterno).
     *
     * Similar a procesarPago, pero usado desde otras vistas del módulo para
     * registrar cobros rápidos.
     *
     * @param \Illuminate\Http\Request $request Datos del pago ingresados por el personal
     * @param \App\Models\Factura $factura Factura pagada
     * @return \Illuminate\Http\RedirectResponse Redirección al dashboard con confirmación
     */
    public function confirmarPago(Request $request, Factura $factura)
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto_recibido' => 'required|numeric|min:' . $factura->total
        ]);

        $factura->update([
            'estado' => 'pagado',
            'metodo_pago' => $request->metodo_pago,
            'monto_recibido' => $request->monto_recibido,
            'fecha_pago' => now(),
            'caja_id' => Auth::id()
        ]);

        // Notificar a recepción
        $recepcionistas = \App\Models\User::where('role', 'recepcionista')->where('activo', 1)->get();
        foreach ($recepcionistas as $recep) {
            \App\Models\NotificacionSistema::create([
                'usuario_emisor_id' => Auth::id(),
                'usuario_receptor_id' => $recep->id,
                'titulo' => 'Pago procesado',
                'mensaje' => 'Paciente: ' . $factura->paciente->nombres . ' ' . $factura->paciente->apellidos . ' - Puede procesar salida',
                'tipo' => 'pago_confirmado',
                'leida' => false
            ]);
        }

        return redirect()->route('caja.dashboard')->with('success', 'Pago procesado exitosamente');
    }

    /**
     * Generar un reporte de ingresos en un rango de fechas.
     *
     * Agrupa facturas pagadas, calcula totales y distribuye los montos por
     * método de pago seleccionado.
     *
     * @param \Illuminate\Http\Request $request Parámetros de filtrado de fechas
     * @return \Illuminate\Contracts\View\View Vista con el resumen de ingresos
     */
    public function reporteIngresos(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        $facturas = Factura::where('estado', 'pagado')
            ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->with(['paciente', 'hospitalizacion'])
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $totalIngresos = $facturas->sum('total');
        $ingresosPorMetodo = $facturas->groupBy('metodo_pago')->map->sum('total');

        return view('caja.reportes.ingresos', compact(
            'facturas',
            'totalIngresos',
            'ingresosPorMetodo',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Notificar al equipo de recepción sobre el pago completado.
     *
     * Envía una notificación interna para que recepción continúe con el proceso
     * de salida del paciente hospitalizado.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización asociada al pago
     * @return void
     */
    private function notificarRecepcion(Hospitalizacion $hospitalizacion)
    {
        try {
            // Buscar recepcionistas activos
            $recepcionistas = \App\Models\User::where('role', 'recepcionista')
                ->where('activo', true)
                ->get();

            foreach ($recepcionistas as $recepcionista) {
                NotificacionSistema::create([
                    'usuario_emisor_id' => Auth::id(),
                    'usuario_receptor_id' => $recepcionista->id,
                    'titulo' => 'Pago Completado - Paciente Listo para Salida',
                    'mensaje' => "El paciente {$hospitalizacion->paciente->getNombreCompletoAttribute()} ha completado el pago y está listo para la salida. Por favor, procede con la encuesta de satisfacción y el alta final.",
                    'tipo' => 'pago_completado',
                    'leida' => false,
                    'data' => [
                        'hospitalizacion_id' => $hospitalizacion->id,
                        'paciente_id' => $hospitalizacion->paciente_id,
                        'factura_id' => $hospitalizacion->facturas()->where('estado', 'pagado')->first()?->id
                    ]
                ]);
            }

            Log::info('Notificación enviada a recepción por pago completado', [
                'caja_id' => Auth::id(),
                'hospitalizacion_id' => $hospitalizacion->id,
                'paciente_id' => $hospitalizacion->paciente_id
            ]);

        } catch (\Exception $e) {
            Log::error('Error al notificar recepción: ' . $e->getMessage());
        }
    }

    /**
     * Buscar facturas según datos del paciente o estado.
     *
     * Filtra por DNI, nombre y estado de la factura para facilitar su gestión.
     *
     * @param \Illuminate\Http\Request $request Filtros aplicados en la búsqueda
     * @return \Illuminate\Contracts\View\View Vista con resultados paginados
     */
    public function buscarFacturas(Request $request)
    {
        $query = Factura::query();

        if ($request->filled('dni')) {
            $query->whereHas('paciente', function($q) use ($request) {
                $q->where('dni', 'like', '%' . $request->dni . '%');
            });
        }

        if ($request->filled('nombre')) {
            $query->whereHas('paciente', function($q) use ($request) {
                $q->where('nombres', 'like', '%' . $request->nombre . '%')
                  ->orWhere('apellidos', 'like', '%' . $request->nombre . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $facturas = $query->with(['paciente', 'hospitalizacion'])
            ->orderBy('fecha_emision', 'desc')
            ->paginate(15);

        return view('caja.facturas.buscar', compact('facturas'));
    }

    /**
     * Realizar el cierre de caja diario para el usuario en sesión.
     *
     * Calcula totales por método de pago, registra la acción en el log y deja
     * constancia de las observaciones ingresadas.
     *
     * @param \Illuminate\Http\Request $request Observaciones opcionales del cierre
     * @return \Illuminate\Http\RedirectResponse Redirección al dashboard con resumen del cierre
     */
    public function cerrarCaja(Request $request)
    {
        $request->validate([
            'observaciones' => 'nullable|string|max:500'
        ]);

        try {
            // Obtener estadísticas del día
            $facturasPagadas = Factura::where('estado', 'pagado')
                ->whereDate('fecha_pago', today())
                ->where('caja_id', Auth::id())
                ->get();

            $totalEfectivo = $facturasPagadas->where('metodo_pago', 'efectivo')->sum('total');
            $totalTarjeta = $facturasPagadas->where('metodo_pago', 'tarjeta')->sum('total');
            $totalTransferencia = $facturasPagadas->where('metodo_pago', 'transferencia')->sum('total');
            $totalGeneral = $facturasPagadas->sum('total');

            // Crear registro de cierre de caja
            $cierreCaja = [
                'fecha' => today(),
                'caja_id' => Auth::id(),
                'total_efectivo' => $totalEfectivo,
                'total_tarjeta' => $totalTarjeta,
                'total_transferencia' => $totalTransferencia,
                'total_general' => $totalGeneral,
                'cantidad_facturas' => $facturasPagadas->count(),
                'observaciones' => $request->observaciones,
                'created_at' => now()
            ];

            // Aquí podrías guardar en una tabla de cierres de caja si la tienes
            Log::info('Cierre de caja realizado', $cierreCaja);

            return redirect()->route('caja.dashboard')
                ->with('success', 'Caja cerrada exitosamente. Total del día: $' . number_format($totalGeneral, 2));

        } catch (\Exception $e) {
            Log::error('Error al cerrar caja: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al cerrar la caja. Inténtalo de nuevo.']);
        }
    }
}