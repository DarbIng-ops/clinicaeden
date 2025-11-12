<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Auth;
use App\Models\Hospitalizacion;

class MedicoEspecialistaController extends Controller
{
    /**
     * Mostrar el panel principal del médico especialista.
     *
     * Compila consultas del día, pacientes recurrentes y tratamientos pendientes
     * para ofrecer un resumen operativo del especialista autenticado.
     *
     * @return \Illuminate\Contracts\View\View Vista con indicadores de la especialidad
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener datos para el dashboard
        $consultasEspecializadas = Consulta::with(['paciente', 'historiaClinica'])
            ->where('medico_id', $user->id)
            ->where('tipo_consulta', 'especializada')
            ->whereDate('fecha_consulta', today())
            ->get();

        $pacientesEspecialidad = Paciente::whereHas('consultas', function($query) use ($user) {
            $query->where('medico_id', $user->id)
                  ->where('tipo_consulta', 'especializada');
        })->with(['consultas' => function($query) use ($user) {
            $query->where('medico_id', $user->id)
                  ->where('tipo_consulta', 'especializada');
        }])->get();

        $tratamientosEspecializados = Consulta::with(['paciente'])
            ->where('medico_id', $user->id)
            ->where('tipo_consulta', 'especializada')
            ->where('estado', 'pendiente')
            ->get();

        $notificacionesNoLeidas = $user->notificacionesNoLeidas()->count();

        return view('medico_especialista.dashboard', compact(
            'consultasEspecializadas',
            'pacientesEspecialidad',
            'tratamientosEspecializados',
            'notificacionesNoLeidas'
        ));
    }

    /**
     * Listar los pacientes atendidos por el especialista.
     *
     * Filtra las historias clínicas por consultas especializadas asociadas
     * al profesional y devuelve un listado paginado.
     *
     * @return \Illuminate\Contracts\View\View Vista con pacientes de la especialidad
     */
    public function pacientes()
    {
        $user = Auth::user();
        
        $pacientes = Paciente::whereHas('consultas', function($query) use ($user) {
            $query->where('medico_id', $user->id)
                  ->where('tipo_consulta', 'especializada');
        })->with(['consultas' => function($query) use ($user) {
            $query->where('medico_id', $user->id)
                  ->where('tipo_consulta', 'especializada');
        }])->paginate(15);

        return view('medico_especialista.pacientes', compact('pacientes'));
    }

    /**
     * Mostrar los detalles de un paciente para el especialista.
     *
     * Valida el acceso comprobando que existan consultas especializadas previas
     * y carga la historia clínica junto con dichas consultas.
     *
     * @param \App\Models\Paciente $paciente Paciente que se desea revisar
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con datos clínicos o redirección por falta de permisos
     */
    public function verPaciente(Paciente $paciente)
    {
        $user = Auth::user();
        
        // Verificar que el médico tenga acceso a este paciente
        $consultaEspecializada = Consulta::where('paciente_id', $paciente->id)
            ->where('medico_id', $user->id)
            ->where('tipo_consulta', 'especializada')
            ->first();

        if (!$consultaEspecializada) {
            return redirect()->route('medico_especialista.dashboard')
                ->with('error', 'No tienes acceso a este paciente.');
        }

        $paciente->load(['historiaClinica', 'consultas' => function($query) use ($user) {
            $query->where('medico_id', $user->id)
                  ->where('tipo_consulta', 'especializada');
        }]);

        return view('medico_especialista.ver-paciente', compact('paciente'));
    }

    /**
     * Mostrar el formulario para registrar una nueva consulta especializada.
     *
     * Solo permite el acceso si el paciente ya tiene seguimiento con el
     * especialista autenticado.
     *
     * @param \App\Models\Paciente $paciente Paciente sobre el que se creará la consulta
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con formulario o redirección por falta de acceso
     */
    public function crearConsulta(Paciente $paciente)
    {
        $user = Auth::user();
        
        // Verificar acceso al paciente
        $consultaEspecializada = Consulta::where('paciente_id', $paciente->id)
            ->where('medico_id', $user->id)
            ->where('tipo_consulta', 'especializada')
            ->first();

        if (!$consultaEspecializada) {
            return redirect()->route('medico_especialista.dashboard')
                ->with('error', 'No tienes acceso a este paciente.');
        }

        return view('medico_especialista.crear-consulta', compact('paciente'));
    }

    /**
     * Registrar una nueva consulta especializada en el sistema.
     *
     * Persiste diagnóstico, tratamiento y especialidad y emite una notificación
     * informativa para el paciente o familiares.
     *
     * @param \Illuminate\Http\Request $request Datos enviados desde el formulario de consulta
     * @param \App\Models\Paciente $paciente Paciente atendido por el especialista
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de confirmación o error
     */
    public function storeConsulta(Request $request, Paciente $paciente)
    {
        $user = Auth::user();
        
        // Verificar acceso al paciente
        $consultaEspecializada = Consulta::where('paciente_id', $paciente->id)
            ->where('medico_id', $user->id)
            ->where('tipo_consulta', 'especializada')
            ->first();

        if (!$consultaEspecializada) {
            return redirect()->route('medico_especialista.dashboard')
                ->with('error', 'No tienes acceso a este paciente.');
        }

        $request->validate([
            'fecha_consulta' => 'required|date',
            'hora_consulta' => 'required',
            'motivo_consulta' => 'required|string|max:500',
            'diagnostico' => 'required|string|max:1000',
            'tratamiento' => 'required|string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
            'especialidad' => 'required|string|max:255',
        ]);

        try {
            $consulta = Consulta::create([
                'historia_clinica_id' => $paciente->historiaClinica->id,
                'medico_id' => $user->id,
                'fecha_consulta' => $request->fecha_consulta,
                'hora_consulta' => $request->hora_consulta,
                'motivo_consulta' => $request->motivo_consulta,
                'diagnostico' => $request->diagnostico,
                'tratamiento' => $request->tratamiento,
                'observaciones' => $request->observaciones,
                'tipo_consulta' => 'especializada',
                'especialidad' => $request->especialidad,
                'estado' => 'completada',
            ]);

            // Crear notificación para el paciente/familia
            NotificacionSistema::crearNotificacion(
                $user->id,
                $paciente->id, // Asumiendo que el paciente tiene un usuario asociado
                'consulta_especializada_completada',
                'Consulta Especializada Completada',
                "Su consulta especializada en {$request->especialidad} ha sido completada por el Dr. {$user->getNombreCompletoAttribute()}",
                ['consulta_id' => $consulta->id, 'especialidad' => $request->especialidad]
            );

            return redirect()->route('medico_especialista.ver-paciente', $paciente)
                ->with('success', 'Consulta especializada creada exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear la consulta: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Mostrar estadísticas relevantes de la especialidad.
     *
     * Calcula volumen de consultas del mes, pacientes atendidos y casos aún pendientes.
     *
     * @return \Illuminate\Contracts\View\View Vista con métricas clave
     */
    public function estadisticas()
    {
        $user = Auth::user();
        
        $estadisticas = [
            'total_consultas_mes' => Consulta::where('medico_id', $user->id)
                ->where('tipo_consulta', 'especializada')
                ->whereMonth('fecha_consulta', now()->month)
                ->count(),
            
            'pacientes_atendidos' => Paciente::whereHas('consultas', function($query) use ($user) {
                $query->where('medico_id', $user->id)
                      ->where('tipo_consulta', 'especializada');
            })->count(),
            
            'consultas_pendientes' => Consulta::where('medico_id', $user->id)
                ->where('tipo_consulta', 'especializada')
                ->where('estado', 'pendiente')
                ->count(),
        ];

        return view('medico_especialista.estadisticas', compact('estadisticas'));
    }

    /**
     * Listar hospitalizaciones que requieren intervención del especialista.
     *
     * Incluye hospitalizaciones con tratamientos que solicitan especialista o
     * consultas especializadas registradas por el profesional actual.
     *
     * @return \Illuminate\Contracts\View\View Vista con hospitalizaciones relevantes
     */
    public function hospitalizaciones()
    {
        $user = Auth::user();
        
        // Obtener hospitalizaciones que requieren especialista o tienen consultas especializadas
        $hospitalizaciones = Hospitalizacion::whereHas('tratamientos', function($query) {
            $query->where('requiere_especialista', true);
        })
        ->orWhereHas('consultas', function($query) use ($user) {
            $query->where('medico_id', $user->id)
                  ->where('tipo_consulta', 'especializada');
        })
        ->with(['paciente', 'habitacion.modulo.piso', 'medicoGeneral'])
        ->orderBy('fecha_ingreso', 'desc')
        ->paginate(15);

        return view('medico_especialista.hospitalizaciones', compact('hospitalizaciones'));
    }
}