<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    /**
     * Listar facturas según el rol del usuario autenticado.
     *
     * Admin y recepción visualizan todas las facturas, mientras que el personal
     * de caja sólo ve aquellas procesadas por su usuario.
     *
     * @return \Illuminate\Contracts\View\View Vista con el listado de facturas
     */
    public function index()
    {
        $user = Auth::user();
        $facturas = collect();

        switch ($user->role) {
            case 'admin':
                $facturas = Factura::with(['paciente', 'hospitalizacion', 'caja'])->get();
                break;
            case 'caja':
                $facturas = $user->facturasComoCaja()->with(['paciente', 'hospitalizacion'])->get();
                break;
            case 'recepcion':
                $facturas = Factura::with(['paciente', 'hospitalizacion', 'caja'])->get();
                break;
        }

        return view('facturas.index', compact('facturas'));
    }

    /**
     * Mostrar formulario para generar una nueva factura.
     *
     * Si se proporciona un paciente, se precarga la última hospitalización en
     * estado de alta de enfermería para agilizar el proceso.
     *
     * @param \Illuminate\Http\Request $request Parámetros opcionales (paciente)
     * @return \Illuminate\Contracts\View\View Vista del formulario de facturación
     */
    public function create(Request $request)
    {
        $paciente = null;
        $hospitalizacion = null;

        if ($request->has('paciente_id')) {
            $paciente = Paciente::findOrFail($request->paciente_id);
            $hospitalizacion = Hospitalizacion::where('paciente_id', $paciente->id)
                ->where('estado', 'alta_enfermeria')
                ->first();
        }

        return view('facturas.create', compact('paciente', 'hospitalizacion'));
    }

    /**
     * Persistir una nueva factura en el sistema.
     *
     * Calcula totales, marca la factura como pagada (flujo inmediato) y
     * actualiza el estado de la hospitalización si corresponde.
     *
     * @param \Illuminate\Http\Request $request Datos de la factura
     * @return \Illuminate\Http\RedirectResponse Redirección a la vista de factura con mensaje de éxito
     * @throws \Illuminate\Validation\ValidationException Si los datos no cumplen las reglas
     */
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'hospitalizacion_id' => 'nullable|exists:hospitalizaciones,id',
            'consulta_id' => 'nullable|exists:consultas,id',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $factura = Factura::create([
                'numero_factura' => $this->generarNumeroFactura(),
                'paciente_id' => $request->paciente_id,
                'hospitalizacion_id' => $request->hospitalizacion_id,
                'consulta_id' => $request->consulta_id,
                'caja_id' => Auth::id(),
                'subtotal' => $request->subtotal,
                'impuestos' => $request->impuestos,
                'total' => $request->subtotal + $request->impuestos,
                'metodo_pago' => $request->metodo_pago,
                'estado' => 'pagado',
                'fecha_emision' => now(),
                'fecha_pago' => now(),
                'observaciones' => $request->observaciones,
            ]);

            // Si es una hospitalización, actualizar el estado
            if ($request->hospitalizacion_id) {
                $hospitalizacion = Hospitalizacion::findOrFail($request->hospitalizacion_id);
                $hospitalizacion->update([
                    'estado' => 'alta_pago',
                    'pago_completado' => true
                ]);

                // Notificar a recepción que el pago está completo
                $recepcionistas = \App\Models\User::where('role', 'recepcion')->where('activo', true)->get();
                foreach ($recepcionistas as $recepcionista) {
                    NotificacionSistema::crearNotificacion(
                        Auth::id(),
                        $recepcionista->id,
                        'pago_completado',
                        'Pago Completado',
                        "El pago de la factura {$factura->numero_factura} ha sido procesado exitosamente. El paciente {$factura->paciente->nombre_completo} puede ser dado de alta.",
                        [
                            'factura_id' => $factura->id,
                            'hospitalizacion_id' => $hospitalizacion->id,
                            'paciente_id' => $factura->paciente_id
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('facturas.show', $factura)->with('success', 'Factura creada y pagada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear la factura: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar los detalles completos de una factura.
     *
     * @param \App\Models\Factura $factura Factura seleccionada
     * @return \Illuminate\Contracts\View\View Vista con la información de la factura
     */
    public function show(Factura $factura)
    {
        $factura->load(['paciente', 'hospitalizacion', 'consulta', 'caja']);
        return view('facturas.show', compact('factura'));
    }

    /**
     * Renderizar la vista imprimible de una factura.
     *
     * @param \App\Models\Factura $factura Factura que se imprimirá
     * @return \Illuminate\Contracts\View\View Vista optimizada para impresión
     */
    public function imprimir(Factura $factura)
    {
        $factura->load(['paciente', 'hospitalizacion', 'consulta', 'caja']);
        return view('facturas.imprimir', compact('factura'));
    }

    /**
     * Buscar rápidamente un paciente por DNI para facturación.
     *
     * Devuelve la hospitalización en curso (si existe) y el costo total estimado.
     *
     * @param \Illuminate\Http\Request $request Solicitud con el DNI a consultar
     * @return \Illuminate\Http\JsonResponse Respuesta con la información del paciente
     */
    public function buscarPaciente(Request $request)
    {
        $dni = $request->input('dni');
        $paciente = Paciente::where('dni', $dni)->where('activo', true)->first();

        if (!$paciente) {
            return response()->json(['error' => 'Paciente no encontrado'], 404);
        }

        $hospitalizacion = Hospitalizacion::where('paciente_id', $paciente->id)
            ->where('estado', 'alta_enfermeria')
            ->first();

        return response()->json([
            'paciente' => $paciente,
            'hospitalizacion' => $hospitalizacion,
            'costo_total' => $hospitalizacion ? $hospitalizacion->costo_total : 0
        ]);
    }

    /**
     * Calcular el costo total de una hospitalización pendiente de facturar.
     *
     * Incluye un cálculo de impuestos para devolver el subtotal, impuestos y total.
     *
     * @param \Illuminate\Http\Request $request Identificador de la hospitalización
     * @return \Illuminate\Http\JsonResponse Resumen de costos calculado
     */
    public function calcularCosto(Request $request)
    {
        $hospitalizacionId = $request->input('hospitalizacion_id');
        $hospitalizacion = Hospitalizacion::findOrFail($hospitalizacionId);
        
        $costo = $hospitalizacion->calcularCostoTotal();
        $impuestos = $costo * 0.22; // 22% de impuestos
        $total = $costo + $impuestos;

        return response()->json([
            'subtotal' => $costo,
            'impuestos' => $impuestos,
            'total' => $total
        ]);
    }

    /**
     * Generar un número de factura incremental.
     *
     * @return string Código de factura con prefijo FAC-
     */
    private function generarNumeroFactura()
    {
        $ultimaFactura = Factura::orderBy('id', 'desc')->first();
        $numero = $ultimaFactura ? $ultimaFactura->id + 1 : 1;
        return 'FAC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Mostrar el reporte de ingresos filtrado por rango de fechas.
     *
     * @param \Illuminate\Http\Request $request Fechas de inicio y fin del reporte
     * @return \Illuminate\Contracts\View\View Vista con métricas de facturación
     */
    public function reporteIngresos(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth());

        $facturas = Factura::whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->where('estado', 'pagado')
            ->with(['paciente', 'hospitalizacion'])
            ->get();

        $totalIngresos = $facturas->sum('total');
        $totalFacturas = $facturas->count();
        $ingresosPorMetodo = $facturas->groupBy('metodo_pago')->map->sum('total');

        return view('facturas.reporte', compact('facturas', 'totalIngresos', 'totalFacturas', 'ingresosPorMetodo', 'fechaInicio', 'fechaFin'));
    }
}