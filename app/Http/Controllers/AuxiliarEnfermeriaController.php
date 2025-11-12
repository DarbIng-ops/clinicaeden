<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospitalizacion;
use App\Models\Tratamiento;
use App\Models\ModuloEnfermeria;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuxiliarEnfermeriaController extends Controller
{
    /**
     * Mostrar el panel principal del auxiliar de enfermería.
     *
     * Reúne la información de módulos asignados, hospitalizaciones a cargo,
     * procedimientos pendientes y productividad diaria.
     *
     * @return \Illuminate\Contracts\View\View Vista con indicadores y listados del auxiliar
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener módulos asignados al auxiliar
        $modulos = $user->modulosComoAuxiliar()->get();

        // Hospitalizaciones asignadas
        $hospitalizacionesAsignadas = Hospitalizacion::where('auxiliar_enfermeria_id', $user->id)
            ->where('estado', 'activo')
            ->with(['paciente', 'habitacion.modulo', 'medicoGeneral'])
            ->orderBy('fecha_ingreso', 'desc')
            ->get();

        // Procedimientos asignados pendientes
        $procedimientosPendientes = Tratamiento::whereHas('hospitalizacion', function($query) use ($user) {
            $query->where('auxiliar_enfermeria_id', $user->id);
        })
        ->where('estado', 'activo')
        ->with(['hospitalizacion.paciente', 'hospitalizacion.habitacion.modulo'])
        ->orderBy('fecha_programada', 'asc')
        ->get();

        // Procedimientos completados hoy
        $procedimientosCompletadosHoy = Tratamiento::whereHas('hospitalizacion', function($query) use ($user) {
            $query->where('auxiliar_enfermeria_id', $user->id);
        })
        ->where('estado', 'completado')
        ->whereDate('fecha_completado', today())
        ->count();

        // Estadísticas del día
        $totalPacientesAsignados = $hospitalizacionesAsignadas->count();
        $procedimientosPendientesCount = $procedimientosPendientes->count();

        return view('auxiliar-enfermeria.dashboard', compact(
            'modulos',
            'hospitalizacionesAsignadas',
            'procedimientosPendientes',
            'procedimientosCompletadosHoy',
            'totalPacientesAsignados',
            'procedimientosPendientesCount'
        ));
    }

    /**
     * Visualizar los detalles de una hospitalización asignada al auxiliar.
     *
     * Verifica el acceso y ordena los tratamientos por fecha programada para
     * facilitar la planificación del cuidado.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización asignada
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con detalles o redirección sin permisos
     */
    public function verHospitalizacion(Hospitalizacion $hospitalizacion)
    {
        // Verificar que la hospitalización está asignada al auxiliar
        if ($hospitalizacion->auxiliar_enfermeria_id !== Auth::id()) {
            return redirect()->route('auxiliar-enfermeria.dashboard')
                ->withErrors(['error' => 'No tienes acceso a esta hospitalización.']);
        }

        $hospitalizacion->load([
            'paciente',
            'habitacion.modulo.piso',
            'medicoGeneral',
            'tratamientos' => function($query) {
                $query->orderBy('fecha_programada', 'asc');
            }
        ]);

        return view('auxiliar-enfermeria.hospitalizaciones.ver', compact('hospitalizacion'));
    }

    /**
     * Listar los procedimientos asignados al auxiliar.
     *
     * @return \Illuminate\Contracts\View\View Vista paginada con tratamientos pendientes y programados
     */
    public function verProcedimientos()
    {
        $user = Auth::user();
        
        $procedimientos = Tratamiento::whereHas('hospitalizacion', function($query) use ($user) {
            $query->where('auxiliar_enfermeria_id', $user->id);
        })
        ->with(['hospitalizacion.paciente', 'hospitalizacion.habitacion.modulo'])
        ->orderBy('fecha_programada', 'asc')
        ->paginate(15);

        return view('auxiliar-enfermeria.procedimientos.index', compact('procedimientos'));
    }

    /**
     * Registrar la finalización de un procedimiento asignado.
     *
     * Captura observaciones, hora de aplicación y comentarios del paciente,
     * además de notificar al jefe de enfermería correspondiente.
     *
     * @param \Illuminate\Http\Request $request Datos del procedimiento realizado
     * @param \App\Models\Tratamiento $tratamiento Tratamiento que se está completando
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de confirmación o error
     */
    public function completarProcedimiento(Request $request, Tratamiento $tratamiento)
    {
        $request->validate([
            'observaciones_procedimiento' => 'nullable|string|max:1000',
            'hora_aplicacion' => 'nullable|date_format:H:i',
            'comentarios_paciente' => 'nullable|string|max:500'
        ]);

        try {
            // Verificar que el procedimiento está asignado al auxiliar
            if ($tratamiento->hospitalizacion->auxiliar_enfermeria_id !== Auth::id()) {
                return redirect()->back()
                    ->withErrors(['error' => 'No tienes acceso a este procedimiento.']);
            }

            $tratamiento->update([
                'estado' => 'completado',
                'observaciones_procedimiento' => $request->observaciones_procedimiento,
                'hora_aplicacion' => $request->hora_aplicacion ?: now()->format('H:i'),
                'fecha_completado' => now(),
                'completado_por' => Auth::id(),
                'comentarios_paciente' => $request->comentarios_paciente
            ]);

            // Notificar al jefe de enfermería
            $this->notificarProcedimientoCompletado($tratamiento);

            Log::info('Procedimiento completado por auxiliar', [
                'auxiliar_id' => Auth::id(),
                'tratamiento_id' => $tratamiento->id,
                'paciente_id' => $tratamiento->hospitalizacion->paciente_id
            ]);

            return redirect()->back()
                ->with('success', 'Procedimiento marcado como completado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al completar procedimiento: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al completar el procedimiento. Inténtalo de nuevo.']);
        }
    }

    /**
     * Registrar un comentario clínico o conductual sobre el paciente.
     *
     * Almacena el comentario dentro de la hospitalización y envía una notificación
     * al jefe de enfermería para seguimiento.
     *
     * @param \Illuminate\Http\Request $request Comentario redactado por el auxiliar
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización relacionada
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de confirmación o error
     */
    public function agregarComentario(Request $request, Hospitalizacion $hospitalizacion)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
            'tipo_comentario' => 'required|in:observacion,comportamiento,estado_fisico,otro'
        ]);

        try {
            // Verificar que la hospitalización está asignada al auxiliar
            if ($hospitalizacion->auxiliar_enfermeria_id !== Auth::id()) {
                return redirect()->back()
                    ->withErrors(['error' => 'No tienes acceso a esta hospitalización.']);
            }

            // Crear comentario en el historial
            $comentario = [
                'fecha' => now()->format('Y-m-d H:i:s'),
                'auxiliar_id' => Auth::id(),
                'auxiliar_nombre' => Auth::user()->getNombreCompletoAttribute(),
                'tipo' => $request->tipo_comentario,
                'comentario' => $request->comentario
            ];

            // Obtener comentarios existentes y agregar el nuevo
            $comentariosExistentes = $hospitalizacion->comentarios_auxiliares ?? [];
            $comentariosExistentes[] = $comentario;

            $hospitalizacion->update([
                'comentarios_auxiliares' => $comentariosExistentes
            ]);

            // Notificar al jefe de enfermería sobre el comentario
            $this->notificarComentarioAgregado($hospitalizacion, $comentario);

            Log::info('Comentario agregado por auxiliar', [
                'auxiliar_id' => Auth::id(),
                'hospitalizacion_id' => $hospitalizacion->id,
                'paciente_id' => $hospitalizacion->paciente_id,
                'tipo_comentario' => $request->tipo_comentario
            ]);

            return redirect()->back()
                ->with('success', 'Comentario agregado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al agregar comentario: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al agregar el comentario. Inténtalo de nuevo.']);
        }
    }

    /**
     * Consultar el historial de comentarios registrados por auxiliares.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización de referencia
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con comentarios o redirección si no hay acceso
     */
    public function verHistorialComentarios(Hospitalizacion $hospitalizacion)
    {
        // Verificar que la hospitalización está asignada al auxiliar
        if ($hospitalizacion->auxiliar_enfermeria_id !== Auth::id()) {
            return redirect()->route('auxiliar-enfermeria.dashboard')
                ->withErrors(['error' => 'No tienes acceso a esta hospitalización.']);
        }

        $comentarios = $hospitalizacion->comentarios_auxiliares ?? [];
        
        return view('auxiliar-enfermeria.hospitalizaciones.historial-comentarios', compact('hospitalizacion', 'comentarios'));
    }

    /**
     * Listar los módulos de enfermería asignados al auxiliar.
     *
     * @return \Illuminate\Contracts\View\View Vista con módulos y contexto organizacional
     */
    public function verModulos()
    {
        $modulos = Auth::user()->modulosComoAuxiliar()
            ->with(['piso', 'jefeEnfermeria', 'habitaciones'])
            ->get();

        return view('auxiliar-enfermeria.modulos.index', compact('modulos'));
    }

    /**
     * Notificar al jefe de enfermería que un procedimiento fue completado.
     *
     * @param \App\Models\Tratamiento $tratamiento Tratamiento finalizado
     * @return void
     */
    private function notificarProcedimientoCompletado(Tratamiento $tratamiento)
    {
        $jefeEnfermeria = $tratamiento->hospitalizacion->habitacion->modulo->jefeEnfermeria;
        
        if ($jefeEnfermeria) {
            NotificacionSistema::create([
                'usuario_emisor_id' => Auth::id(),
                'usuario_receptor_id' => $jefeEnfermeria->id,
                'titulo' => 'Procedimiento Completado',
                'mensaje' => "El auxiliar " . Auth::user()->getNombreCompletoAttribute() . " ha completado el procedimiento para el paciente {$tratamiento->hospitalizacion->paciente->getNombreCompletoAttribute()}.",
                'tipo' => 'procedimiento_completado',
                'leida' => false,
                'data' => [
                    'tratamiento_id' => $tratamiento->id,
                    'hospitalizacion_id' => $tratamiento->hospitalizacion_id,
                    'paciente_id' => $tratamiento->hospitalizacion->paciente_id,
                    'auxiliar_id' => Auth::id()
                ]
            ]);
        }
    }

    /**
     * Notificar al jefe de enfermería sobre un nuevo comentario del auxiliar.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización comentada
     * @param array $comentario Comentario registrado con metadatos
     * @return void
     */
    private function notificarComentarioAgregado(Hospitalizacion $hospitalizacion, array $comentario)
    {
        $jefeEnfermeria = $hospitalizacion->habitacion->modulo->jefeEnfermeria;
        
        if ($jefeEnfermeria) {
            NotificacionSistema::create([
                'usuario_emisor_id' => Auth::id(),
                'usuario_receptor_id' => $jefeEnfermeria->id,
                'titulo' => 'Nuevo Comentario de Auxiliar',
                'mensaje' => "El auxiliar " . Auth::user()->getNombreCompletoAttribute() . " ha agregado un comentario sobre el paciente {$hospitalizacion->paciente->getNombreCompletoAttribute()}: " . substr($comentario['comentario'], 0, 100) . "...",
                'tipo' => 'comentario_auxiliar',
                'leida' => false,
                'data' => [
                    'hospitalizacion_id' => $hospitalizacion->id,
                    'paciente_id' => $hospitalizacion->paciente_id,
                    'auxiliar_id' => Auth::id(),
                    'tipo_comentario' => $comentario['tipo']
                ]
            ]);
        }
    }
}