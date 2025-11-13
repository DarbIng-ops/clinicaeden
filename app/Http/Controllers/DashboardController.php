<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\Factura;
use App\Models\EncuestaSatisfaccion;
use App\Models\Habitacion;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Redirigir al usuario al dashboard correspondiente según su rol.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirigir según el rol del usuario
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'recepcionista':
                return redirect()->route('recepcion.dashboard');
            case 'medico_general':
                return redirect()->route('medico_general.dashboard');
            case 'medico_especialista':
                return redirect()->route('medico_especialista.dashboard');
            case 'jefe_enfermeria':
                return redirect()->route('jefe-enfermeria.dashboard');
            case 'auxiliar_enfermeria':
                return redirect()->route('auxiliar-enfermeria.dashboard');
            case 'caja':
                return redirect()->route('caja.dashboard');
            default:
                // Si no tiene rol definido, mostrar dashboard básico
                return view('dashboard.basic', compact('user'));
        }
    }

    /**
     * Mostrar el dashboard ejecutivo para administradores.
     *
     * Incluye métricas de pacientes, ocupación, ingresos, satisfacción y
     * notificaciones relevantes.
     *
     * @return \Illuminate\Contracts\View\View Vista con indicadores administrativos
     */
    public function dashboardAdmin()
    {
        // Estadísticas generales
        $totalPacientes = Paciente::count();
        $pacientesActivos = Paciente::where('activo', true)->count();
        $hospitalizacionesActivas = Hospitalizacion::activas()->count();
        $totalHabitaciones = Habitacion::count();
        $habitacionesDisponibles = Habitacion::disponibles()->count();

        // Ingresos del mes
        $ingresosMes = Factura::whereMonth('fecha_emision', now()->month)
            ->whereYear('fecha_emision', now()->year)
            ->where('estado', 'pagado')
            ->sum('total');

        // Satisfacción del cliente
        $satisfaccionPromedio = \App\Models\EncuestaSatisfaccion::avg('calidad_general') ?? 0;

        // Ocupación por piso - Consulta optimizada para MySQL
        $ocupacionPorPiso = DB::table('habitaciones')
            ->join('modulos_enfermeria', 'habitaciones.modulo_id', '=', 'modulos_enfermeria.id')
            ->join('pisos', 'modulos_enfermeria.piso_id', '=', 'pisos.id')
            ->leftJoin('hospitalizaciones', function($join) {
                $join->on('habitaciones.id', '=', 'hospitalizaciones.habitacion_id')
                     ->where('hospitalizaciones.estado', '=', 'activo');
            })
            ->select(
                'pisos.numero as piso',
                DB::raw('COUNT(DISTINCT habitaciones.id) as total_habitaciones'),
                DB::raw('COUNT(DISTINCT hospitalizaciones.id) as habitaciones_ocupadas')
            )
            ->groupBy('pisos.id', 'pisos.numero')
            ->orderBy('pisos.numero')
            ->get();

        // Pacientes por especialidad (si hay consultas)
        $pacientesPorEspecialidad = DB::table('consultas')
            ->join('users', 'consultas.medico_id', '=', 'users.id')
            ->whereNotNull('users.especialidad')
            ->select('users.especialidad', DB::raw('COUNT(DISTINCT consultas.paciente_id) as total_pacientes'))
            ->groupBy('users.especialidad')
            ->get();

        // Notificaciones recientes
        $notificaciones = NotificacionSistema::where('usuario_receptor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalPacientes',
            'pacientesActivos',
            'hospitalizacionesActivas',
            'totalHabitaciones',
            'habitacionesDisponibles',
            'ingresosMes',
            'satisfaccionPromedio',
            'ocupacionPorPiso',
            'pacientesPorEspecialidad',
            'notificaciones'
        ));
    }

    /**
     * Mostrar el panel de control del personal de recepción.
     *
     * Resume la actividad diaria de pacientes, pendientes de alta y encuestas.
     *
     * @return \Illuminate\Contracts\View\View Vista con métricas y notificaciones de recepción
     */
    public function dashboardRecepcion()
    {
        // Pacientes registrados hoy
        $pacientesHoy = Paciente::whereDate('created_at', today())->count();
        
        // Hospitalizaciones pendientes de alta
        $hospitalizacionesPendientes = Hospitalizacion::where('estado', 'alta_enfermeria')->count();
        
        // Facturas pendientes de pago
        $facturasPendientes = Factura::where('estado', 'pendiente')->count();

        // Pacientes listos para salida
        $pacientesListosParaSalida = Factura::where('estado', 'pagado')
            ->whereHas('consulta', function($q) {
                $q->where('estado', 'completada');
            })
            ->whereDoesntHave('consulta.encuestaSatisfaccion')
            ->whereDate('fecha_pago', '>=', now()->subDays(1))
            ->count();
        
        // Encuestas pendientes
        $encuestasPendientes = $pacientesListosParaSalida;
        
        // Notificaciones
        $notificaciones = NotificacionSistema::where('usuario_receptor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.recepcion', compact(
            'pacientesHoy',
            'hospitalizacionesPendientes',
            'facturasPendientes',
            'pacientesListosParaSalida',
            'encuestasPendientes',
            'notificaciones'
        ));
    }

    /**
     * Mostrar el dashboard operativo del médico general.
     *
     * Presenta pacientes hospitalizados, consultas del día y tratamientos activos.
     *
     * @return \Illuminate\Contracts\View\View Vista con información clínica del médico
     */
    public function dashboardMedicoGeneral()
    {
        // Pacientes hospitalizados por el médico
        $pacientesHospitalizados = Auth::user()->hospitalizacionesComoMedicoGeneral()
            ->activas()
            ->with(['paciente', 'habitacion'])
            ->get();

        // Consultas del día
        $consultasHoy = Auth::user()->consultas()
            ->whereDate('fecha_consulta', today())
            ->with('paciente')
            ->get();

        // Tratamientos pendientes
        $tratamientosPendientes = Auth::user()->hospitalizacionesComoMedicoGeneral()
            ->whereHas('tratamientos', function($query) {
                $query->where('estado', 'activo');
            })
            ->with(['paciente', 'tratamientos'])
            ->get();

        // Notificaciones
        $notificaciones = NotificacionSistema::where('usuario_receptor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.medico-general', compact(
            'pacientesHospitalizados',
            'consultasHoy',
            'tratamientosPendientes',
            'notificaciones'
        ));
    }

    /**
     * Mostrar el dashboard del médico especialista.
     *
     * Destaca consultas especializadas del día y pacientes que requieren atención.
     *
     * @return \Illuminate\Contracts\View\View Vista con información especializada
     */
    public function dashboardMedicoEspecialista()
    {
        // Consultas especializadas del día
        $consultasHoy = Auth::user()->consultas()
            ->whereDate('fecha_consulta', today())
            ->with('paciente')
            ->get();

        // Pacientes que requieren especialista
        $pacientesEspecialista = Hospitalizacion::whereHas('tratamientos', function($query) {
            $query->where('requiere_especialista', true);
        })
        ->activas()
        ->with(['paciente', 'habitacion', 'medicoGeneral'])
        ->get();

        // Notificaciones
        $notificaciones = NotificacionSistema::where('usuario_receptor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.medico-especialista', compact(
            'consultasHoy',
            'pacientesEspecialista',
            'notificaciones'
        ));
    }

    /**
     * Mostrar el panel de supervisión del jefe de enfermería.
     *
     * Resalta pacientes a su cargo, auxiliares disponibles y tratamientos por asignar.
     *
     * @return \Illuminate\Contracts\View\View Vista con recursos de supervisión
     */
    public function dashboardJefeEnfermeria()
    {
        // Pacientes bajo su supervisión
        $pacientesSupervision = Auth::user()->hospitalizacionesComoJefeEnfermeria()
            ->activas()
            ->with(['paciente', 'habitacion', 'auxiliarEnfermeria'])
            ->get();

        // Auxiliares bajo su mando
        $auxiliares = Auth::user()->auxiliares()->where('activo', true)->get();

        // Tratamientos pendientes de asignación
        $tratamientosPendientes = Auth::user()->hospitalizacionesComoJefeEnfermeria()
            ->whereHas('tratamientos', function($query) {
                $query->where('estado', 'activo')
                      ->whereNull('auxiliar_enfermeria_id');
            })
            ->with(['paciente', 'tratamientos'])
            ->get();

        // Notificaciones
        $notificaciones = NotificacionSistema::where('usuario_receptor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.jefe-enfermeria', compact(
            'pacientesSupervision',
            'auxiliares',
            'tratamientosPendientes',
            'notificaciones'
        ));
    }

    /**
     * Mostrar el dashboard del auxiliar de enfermería.
     *
     * Presenta pacientes y tratamientos asignados, además del jefe responsable.
     *
     * @return \Illuminate\Contracts\View\View Vista con información operativa del auxiliar
     */
    public function dashboardAuxiliarEnfermeria()
    {
        // Pacientes asignados
        $pacientesAsignados = Auth::user()->hospitalizacionesComoAuxiliar()
            ->activas()
            ->with(['paciente', 'habitacion', 'jefeEnfermeria'])
            ->get();

        // Tratamientos asignados
        $tratamientosAsignados = Auth::user()->hospitalizacionesComoAuxiliar()
            ->whereHas('tratamientos', function($query) {
                $query->where('estado', 'activo');
            })
            ->with(['paciente', 'tratamientos'])
            ->get();

        // Jefe de enfermería
        $jefeEnfermeria = Auth::user()->jefeEnfermeria;

        // Notificaciones
        $notificaciones = NotificacionSistema::where('usuario_receptor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.auxiliar-enfermeria', compact(
            'pacientesAsignados',
            'tratamientosAsignados',
            'jefeEnfermeria',
            'notificaciones'
        ));
    }

    /**
     * Mostrar el dashboard del área de caja.
     *
     * Resume facturas emitidas en el día, ingresos, pendientes y metodología de pago.
     *
     * @return \Illuminate\Contracts\View\View Vista con indicadores financieros diarios
     */
    public function dashboardCaja()
    {
        // Facturas del día
        $facturasHoy = Auth::user()->facturasComoCaja()
            ->whereDate('fecha_emision', today())
            ->with('paciente')
            ->get();

        // Ingresos del día
        $ingresosHoy = $facturasHoy->sum('total');

        // Facturas pendientes
        $facturasPendientes = Factura::where('estado', 'pendiente')
            ->with('paciente')
            ->get();

        // Métodos de pago más utilizados
        $metodosPago = Factura::where('estado', 'pagado')
            ->whereDate('fecha_emision', today())
            ->select('metodo_pago', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(total) as total'))
            ->groupBy('metodo_pago')
            ->get();

        // Notificaciones
        $notificaciones = NotificacionSistema::where('usuario_receptor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.caja', compact(
            'facturasHoy',
            'ingresosHoy',
            'facturasPendientes',
            'metodosPago',
            'notificaciones'
        ));
    }
}