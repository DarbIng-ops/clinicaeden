<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospitalizacion;
use App\Models\ModuloEnfermeria;
use App\Models\Habitacion;
use App\Models\Tratamiento;
use App\Models\NotificacionSistema;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class JefeEnfermeriaController extends Controller
{
    /**
     * Mostrar el panel principal del jefe de enfermería.
     *
     * Agrupa módulos asignados, hospitalizaciones activas y tratamientos que
     * requieren revisión para ofrecer un panorama operativo completo.
     *
     * @return \Illuminate\Contracts\View\View Vista con estadísticas y listados relevantes
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener módulos asignados al jefe
        $modulos = ModuloEnfermeria::where('jefe_enfermeria_id', $user->id)
            ->with(['piso', 'auxiliares', 'habitaciones'])
            ->activos()
            ->get();

        // Optimización: Obtener IDs de módulos y habitaciones primero
        $modulosIds = ModuloEnfermeria::where('jefe_enfermeria_id', $user->id)->pluck('id');
        $habitacionesIds = Habitacion::whereIn('modulo_id', $modulosIds)->pluck('id');

        // Hospitalizaciones en sus módulos (optimizado)
        $hospitalizaciones = Hospitalizacion::whereIn('habitacion_id', $habitacionesIds)
            ->with(['paciente', 'habitacion.modulo', 'medicoGeneral', 'auxiliarEnfermeria'])
            ->where('estado', 'activo')
            ->orderBy('fecha_ingreso', 'desc')
            ->get();

        // Tratamientos pendientes de revisión (optimizado)
        $tratamientosPendientes = Tratamiento::whereHas('hospitalizacion', function($q) use ($habitacionesIds) {
                $q->whereIn('habitacion_id', $habitacionesIds);
            })
            ->where('estado', 'pendiente_revision')
            ->with(['hospitalizacion.paciente', 'hospitalizacion.habitacion.modulo'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Estadísticas del módulo
        $totalHabitaciones = $modulos->sum(function($modulo) {
            return $modulo->habitaciones->count();
        });

        $totalCamas = $modulos->sum(function($modulo) {
            return $modulo->habitaciones->sum('capacidad');
        });

        $camasOcupadas = $hospitalizaciones->count();
        $camasDisponibles = $totalCamas - $camasOcupadas;

        return view('jefe-enfermeria.dashboard', compact(
            'modulos',
            'hospitalizaciones',
            'tratamientosPendientes',
            'totalHabitaciones',
            'totalCamas',
            'camasOcupadas',
            'camasDisponibles'
        ));
    }

    /**
     * Mostrar los detalles de una hospitalización gestionada por el jefe.
     *
     * Verifica el acceso según el módulo asignado y carga información clínica
     * del paciente, tratamientos y personal involucrado.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización que se desea revisar
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con detalles o redirección cuando no hay permisos
     */
    public function verHospitalizacion(Hospitalizacion $hospitalizacion)
    {
        // Verificar que la hospitalización pertenece a sus módulos
        if (!$this->puedeAccederHospitalizacion($hospitalizacion)) {
            return redirect()->route('jefe-enfermeria.dashboard')
                ->withErrors(['error' => 'No tienes acceso a esta hospitalización.']);
        }

        $hospitalizacion->load([
            'paciente',
            'habitacion.modulo.piso',
            'medicoGeneral',
            'auxiliarEnfermeria',
            'tratamientos' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        return view('jefe-enfermeria.hospitalizaciones.ver', compact('hospitalizacion'));
    }

    /**
     * Revisar un tratamiento indicado y registrar la decisión del jefe.
     *
     * Permite aprobar, rechazar o modificar tratamientos, dejando observaciones
     * y notificando al auxiliar responsable cuando corresponda.
     *
     * @param \Illuminate\Http\Request $request Datos de revisión y ajustes
     * @param \App\Models\Tratamiento $tratamiento Tratamiento evaluado
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de resultado
     */
    public function revisarTratamiento(Request $request, Tratamiento $tratamiento)
    {
        $request->validate([
            'estado' => 'required|in:aprobado,rechazado,modificado',
            'observaciones' => 'nullable|string|max:1000',
            'dosis_ajustada' => 'nullable|string|max:255',
            'frecuencia_ajustada' => 'nullable|string|max:255'
        ]);

        try {
            // Verificar que puede acceder al tratamiento
            if (!$this->puedeAccederTratamiento($tratamiento)) {
                return redirect()->back()
                    ->withErrors(['error' => 'No tienes acceso a este tratamiento.']);
            }

            $datosActualizacion = [
                'estado' => $request->estado,
                'observaciones_jefe_enfermeria' => $request->observaciones,
                'revisado_por' => Auth::id(),
                'fecha_revision' => now()
            ];

            // Si se modificó, agregar datos ajustados
            if ($request->estado === 'modificado') {
                $datosActualizacion['dosis'] = $request->dosis_ajustada;
                $datosActualizacion['frecuencia'] = $request->frecuencia_ajustada;
            }

            $tratamiento->update($datosActualizacion);

            // Notificar al auxiliar asignado
            if ($tratamiento->hospitalizacion->auxiliar_enfermeria_id) {
                $this->notificarAuxiliar($tratamiento);
            }

            Log::info('Tratamiento revisado por jefe de enfermería', [
                'jefe_id' => Auth::id(),
                'tratamiento_id' => $tratamiento->id,
                'estado' => $request->estado,
                'paciente_id' => $tratamiento->hospitalizacion->paciente_id
            ]);

            return redirect()->back()
                ->with('success', 'Tratamiento revisado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al revisar tratamiento: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al revisar el tratamiento. Inténtalo de nuevo.']);
        }
    }

    /**
     * Asignar un auxiliar de enfermería a una hospitalización.
     *
     * Valida que la hospitalización y el auxiliar pertenezcan a los módulos del
     * jefe y emite una notificación al personal asignado.
     *
     * @param \Illuminate\Http\Request $request Identificador del auxiliar seleccionado
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización que recibirá la asignación
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de confirmación o error
     */
    public function asignarAuxiliar(Request $request, Hospitalizacion $hospitalizacion)
    {
        $request->validate([
            'auxiliar_id' => 'required|exists:users,id'
        ]);

        try {
            // Verificar que puede acceder a la hospitalización
            if (!$this->puedeAccederHospitalizacion($hospitalizacion)) {
                return redirect()->back()
                    ->withErrors(['error' => 'No tienes acceso a esta hospitalización.']);
            }

            // Verificar que el auxiliar pertenece a sus módulos
            $auxiliar = User::find($request->auxiliar_id);
            if (!$this->auxiliarPerteneceAModulos($auxiliar)) {
                return redirect()->back()
                    ->withErrors(['error' => 'El auxiliar no pertenece a tus módulos.']);
            }

            $hospitalizacion->update([
                'auxiliar_enfermeria_id' => $request->auxiliar_id
            ]);

            // Notificar al auxiliar
            $this->notificarAsignacionAuxiliar($hospitalizacion, $auxiliar);

            Log::info('Auxiliar asignado a hospitalización', [
                'jefe_id' => Auth::id(),
                'hospitalizacion_id' => $hospitalizacion->id,
                'auxiliar_id' => $request->auxiliar_id,
                'paciente_id' => $hospitalizacion->paciente_id
            ]);

            return redirect()->back()
                ->with('success', 'Auxiliar asignado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al asignar auxiliar: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al asignar el auxiliar. Inténtalo de nuevo.']);
        }
    }

    /**
     * Registrar el alta de enfermería de una hospitalización.
     *
     * Actualiza el estado clínico, conserva observaciones y coordina con
     * recepción para los pasos posteriores.
     *
     * @param \Illuminate\Http\Request $request Observaciones del alta
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización en la que se otorga el alta
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje del resultado
     */
    public function darAltaEnfermeria(Request $request, Hospitalizacion $hospitalizacion)
    {
        $request->validate([
            'observaciones_alta' => 'nullable|string|max:1000'
        ]);

        try {
            // Verificar que puede acceder a la hospitalización
            if (!$this->puedeAccederHospitalizacion($hospitalizacion)) {
                return redirect()->back()
                    ->withErrors(['error' => 'No tienes acceso a esta hospitalización.']);
            }

            // Verificar que el médico haya dado alta médica primero
            if (!$hospitalizacion->fecha_alta_medica) {
                return redirect()->back()
                    ->withErrors(['error' => 'El médico debe dar alta médica antes del alta de enfermería.']);
            }

            $hospitalizacion->update([
                'estado' => 'alta_enfermeria',
                'observaciones_alta_enfermeria' => $request->observaciones_alta,
                'fecha_alta_enfermeria' => now()
            ]);

            // Notificar a recepción
            $this->notificarAltaEnfermeria($hospitalizacion);

            Log::info('Alta de enfermería otorgada', [
                'jefe_id' => Auth::id(),
                'hospitalizacion_id' => $hospitalizacion->id,
                'paciente_id' => $hospitalizacion->paciente_id
            ]);

            return redirect()->back()
                ->with('success', 'Alta de enfermería otorgada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al dar alta de enfermería: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error al dar el alta de enfermería. Inténtalo de nuevo.']);
        }
    }

    /**
     * Listar los auxiliares activos asociados a un módulo.
     *
     * Verifica que el módulo corresponda al jefe autenticado antes de mostrar
     * el personal y sus hospitalizaciones activas.
     *
     * @param \App\Models\ModuloEnfermeria $modulo Módulo que se desea inspeccionar
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con auxiliares o redirección sin permisos
     */
    public function verAuxiliares(ModuloEnfermeria $modulo)
    {
        // Verificar que el módulo pertenece al jefe
        if ($modulo->jefe_enfermeria_id !== Auth::id()) {
            return redirect()->route('jefe-enfermeria.dashboard')
                ->withErrors(['error' => 'No tienes acceso a este módulo.']);
        }

        $auxiliares = $modulo->auxiliares()->activos()->get();
        $auxiliares->load(['hospitalizacionesComoAuxiliar' => function($query) {
            $query->where('estado', 'activo');
        }]);

        return view('jefe-enfermeria.modulos.auxiliares', compact('modulo', 'auxiliares'));
    }

    /**
     * Determinar si el jefe puede acceder a la hospitalización indicada.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización que se valida
     * @return bool Verdadero si la hospitalización pertenece a un módulo del jefe
     */
    private function puedeAccederHospitalizacion(Hospitalizacion $hospitalizacion)
    {
        return $hospitalizacion->habitacion->modulo->jefe_enfermeria_id === Auth::id();
    }

    /**
     * Verificar si el jefe tiene acceso al tratamiento proporcionado.
     *
     * @param \App\Models\Tratamiento $tratamiento Tratamiento que se valida
     * @return bool Verdadero si el tratamiento corresponde a sus módulos
     */
    private function puedeAccederTratamiento(Tratamiento $tratamiento)
    {
        return $tratamiento->hospitalizacion->habitacion->modulo->jefe_enfermeria_id === Auth::id();
    }

    /**
     * Confirmar si un auxiliar forma parte de los módulos del jefe.
     *
     * @param \App\Models\User $auxiliar Auxiliar que se evalúa
     * @return bool Verdadero cuando el auxiliar está asignado a los módulos del jefe
     */
    private function auxiliarPerteneceAModulos(User $auxiliar)
    {
        $modulosJefe = ModuloEnfermeria::where('jefe_enfermeria_id', Auth::id())->pluck('id');
        return $auxiliar->modulosComoAuxiliar()->whereIn('modulo_id', $modulosJefe)->exists();
    }

    /**
     * Notificar al auxiliar asignado sobre la revisión de un tratamiento.
     *
     * @param \App\Models\Tratamiento $tratamiento Tratamiento revisado
     * @return void
     */
    private function notificarAuxiliar(Tratamiento $tratamiento)
    {
        if (!$tratamiento->hospitalizacion->auxiliar_enfermeria_id) {
            return;
        }

        NotificacionSistema::create([
            'usuario_emisor_id' => Auth::id(),
            'usuario_receptor_id' => $tratamiento->hospitalizacion->auxiliar_enfermeria_id,
            'titulo' => 'Tratamiento Revisado',
            'mensaje' => "El tratamiento para el paciente {$tratamiento->hospitalizacion->paciente->getNombreCompletoAttribute()} ha sido revisado. Estado: " . ucfirst($tratamiento->estado),
            'tipo' => 'tratamiento_revisado',
            'leida' => false,
            'data' => [
                'tratamiento_id' => $tratamiento->id,
                'hospitalizacion_id' => $tratamiento->hospitalizacion_id,
                'paciente_id' => $tratamiento->hospitalizacion->paciente_id
            ]
        ]);
    }

    /**
     * Notificar al auxiliar sobre una nueva asignación de paciente.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización que requiere atención
     * @param \App\Models\User $auxiliar Auxiliar notificado
     * @return void
     */
    private function notificarAsignacionAuxiliar(Hospitalizacion $hospitalizacion, User $auxiliar)
    {
        NotificacionSistema::create([
            'usuario_emisor_id' => Auth::id(),
            'usuario_receptor_id' => $auxiliar->id,
            'titulo' => 'Nueva Asignación',
            'mensaje' => "Has sido asignado al paciente {$hospitalizacion->paciente->getNombreCompletoAttribute()} en la habitación {$hospitalizacion->habitacion->numero}.",
            'tipo' => 'asignacion_paciente',
            'leida' => false,
            'data' => [
                'hospitalizacion_id' => $hospitalizacion->id,
                'paciente_id' => $hospitalizacion->paciente_id,
                'habitacion_id' => $hospitalizacion->habitacion_id
            ]
        ]);
    }

    /**
     * Notificar al personal de recepción sobre el alta de enfermería.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización dada de alta
     * @return void
     */
    private function notificarAltaEnfermeria(Hospitalizacion $hospitalizacion)
    {
        $recepcionistas = User::where('role', 'recepcionista')
            ->where('activo', true)
            ->get();

        foreach ($recepcionistas as $recepcionista) {
            NotificacionSistema::create([
                'usuario_emisor_id' => Auth::id(),
                'usuario_receptor_id' => $recepcionista->id,
                'titulo' => 'Alta de Enfermería Otorgada',
                'mensaje' => "El paciente {$hospitalizacion->paciente->getNombreCompletoAttribute()} ha recibido el alta de enfermería. Pendiente: alta médica y pago.",
                'tipo' => 'alta_enfermeria',
                'leida' => false,
                'data' => [
                    'hospitalizacion_id' => $hospitalizacion->id,
                    'paciente_id' => $hospitalizacion->paciente_id
                ]
            ]);
        }
    }
}