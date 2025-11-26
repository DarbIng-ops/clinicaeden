<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospitalizacion;
use App\Models\Habitacion;
use App\Models\Paciente;
use App\Models\User;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HospitalizacionController extends Controller
{
    /**
     * Listar hospitalizaciones según el rol del usuario autenticado.
     *
     * Admin ve todas las hospitalizaciones; médicos generales, jefes y auxiliares
     * visualizan únicamente las asignadas a su cargo.
     *
     * @return \Illuminate\Contracts\View\View Vista con el conjunto de hospitalizaciones accesibles
     */
    public function index()
    {
        $user = Auth::user();
        $hospitalizaciones = collect();

        switch ($user->role) {
            case 'admin':
                $hospitalizaciones = Hospitalizacion::with(['paciente', 'habitacion', 'medicoGeneral', 'jefeEnfermeria', 'auxiliarEnfermeria'])->get();
                break;
            case 'medico_general':
                $hospitalizaciones = $user->hospitalizacionesComoMedicoGeneral()->with(['paciente', 'habitacion', 'jefeEnfermeria', 'auxiliarEnfermeria'])->get();
                break;
            case 'jefe_enfermeria':
                $hospitalizaciones = $user->hospitalizacionesComoJefeEnfermeria()->with(['paciente', 'habitacion', 'medicoGeneral', 'auxiliarEnfermeria'])->get();
                break;
            case 'auxiliar_enfermeria':
                $hospitalizaciones = $user->hospitalizacionesComoAuxiliar()->with(['paciente', 'habitacion', 'medicoGeneral', 'jefeEnfermeria'])->get();
                break;
        }

        return view('hospitalizaciones.index', compact('hospitalizaciones'));
    }

    /**
     * Mostrar el formulario para crear una nueva hospitalización.
     *
     * Carga catálogos de pacientes activos, habitaciones disponibles y personal
     * clave (jefes y auxiliares) para facilitar la asignación inicial.
     *
     * @return \Illuminate\Contracts\View\View Vista con datos necesarios para hospitalizar
     */
    public function create()
    {
        $pacientes = Paciente::where('activo', true)->get();
        $habitaciones = Habitacion::disponibles()->get();
        $jefesEnfermeria = User::where('role', 'jefe_enfermeria')->where('activo', true)->get();
        $auxiliares = User::where('role', 'auxiliar_enfermeria')->where('activo', true)->get();

        return view('hospitalizaciones.create', compact('pacientes', 'habitaciones', 'jefesEnfermeria', 'auxiliares'));
    }

    /**
     * Registrar una nueva hospitalización en el sistema.
     *
     * Valida disponibilidad de la habitación, crea la hospitalización y notifica
     * al personal correspondiente.
     *
     * @param \Illuminate\Http\Request $request Datos capturados desde el formulario
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito o error
     * @throws \Illuminate\Validation\ValidationException Si los datos proporcionados son inválidos
     */
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'habitacion_id' => 'required|exists:habitaciones,id',
            'motivo_hospitalizacion' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Verificar que la habitación tenga capacidad disponible (con lock pesimista)
            $habitacion = Habitacion::lockForUpdate()->findOrFail($request->habitacion_id);
            if (!$habitacion->tieneCapacidadDisponible()) {
                DB::rollback();
                return back()->withErrors(['habitacion_id' => 'La habitación seleccionada no tiene capacidad disponible.']);
            }

            // Crear la hospitalización
            $hospitalizacion = Hospitalizacion::create([
                'paciente_id' => $request->paciente_id,
                'habitacion_id' => $request->habitacion_id,
                'medico_general_id' => Auth::id(),
                'jefe_enfermeria_id' => $request->jefe_enfermeria_id,
                'auxiliar_enfermeria_id' => $request->auxiliar_enfermeria_id,
                'fecha_ingreso' => now(),
                'estado' => 'activo',
                'motivo_hospitalizacion' => $request->motivo_hospitalizacion,
                'observaciones' => $request->observaciones,
            ]);

            // Notificar al jefe de enfermería si se asignó
            if ($request->jefe_enfermeria_id) {
                NotificacionSistema::crearNotificacion(
                    Auth::id(),
                    $request->jefe_enfermeria_id,
                    'nuevo_paciente_hospitalizado',
                    'Nuevo Paciente Hospitalizado',
                    "Se ha hospitalizado al paciente {$hospitalizacion->paciente->nombre_completo} en la habitación {$habitacion->numero}",
                    [
                        'hospitalizacion_id' => $hospitalizacion->id,
                        'paciente_id' => $hospitalizacion->paciente_id,
                        'habitacion_id' => $habitacion->id
                    ]
                );
            }

            DB::commit();
            return redirect()->route('hospitalizaciones.index')->with('success', 'Paciente hospitalizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al hospitalizar al paciente: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar los detalles de una hospitalización específica.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización seleccionada
     * @return \Illuminate\Contracts\View\View Vista con información clínica y personal asignado
     */
    public function show(Hospitalizacion $hospitalizacion)
    {
        $hospitalizacion->load(['paciente', 'habitacion', 'medicoGeneral', 'jefeEnfermeria', 'auxiliarEnfermeria', 'tratamientos']);
        return view('hospitalizaciones.show', compact('hospitalizacion'));
    }

    /**
     * Asignar un auxiliar de enfermería a una hospitalización.
     *
     * @param \Illuminate\Http\Request $request Identificador del auxiliar seleccionado
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización que recibirá el auxiliar
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje del resultado
     */
    public function asignarAuxiliar(Request $request, Hospitalizacion $hospitalizacion)
    {
        $request->validate([
            'auxiliar_enfermeria_id' => 'required|exists:users,id',
        ]);

        $auxiliar = User::findOrFail($request->auxiliar_enfermeria_id);
        
        if ($auxiliar->role !== 'auxiliar_enfermeria') {
            return back()->withErrors(['auxiliar_enfermeria_id' => 'El usuario seleccionado no es un auxiliar de enfermería.']);
        }

        $hospitalizacion->update(['auxiliar_enfermeria_id' => $auxiliar->id]);

        // Notificar al auxiliar
        NotificacionSistema::crearNotificacion(
            Auth::id(),
            $auxiliar->id,
            'paciente_asignado',
            'Paciente Asignado',
            "Se te ha asignado el paciente {$hospitalizacion->paciente->nombre_completo} en la habitación {$hospitalizacion->habitacion->numero}",
            [
                'hospitalizacion_id' => $hospitalizacion->id,
                'paciente_id' => $hospitalizacion->paciente_id
            ]
        );

        return back()->with('success', 'Auxiliar asignado exitosamente.');
    }

    /**
     * Registrar el alta médica de una hospitalización.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización que recibe la alta médica
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de confirmación
     */
    public function darAltaMedica(Hospitalizacion $hospitalizacion)
    {
        $hospitalizacion->update(['estado' => 'alta_medica']);

        // Notificar al jefe de enfermería
        if ($hospitalizacion->jefe_enfermeria_id) {
            NotificacionSistema::crearNotificacion(
                Auth::id(),
                $hospitalizacion->jefe_enfermeria_id,
                'alta_medica_autorizada',
                'Alta Médica Autorizada',
                "El médico ha autorizado el alta médica para el paciente {$hospitalizacion->paciente->nombre_completo}",
                [
                    'hospitalizacion_id' => $hospitalizacion->id,
                    'paciente_id' => $hospitalizacion->paciente_id
                ]
            );
        }

        return back()->with('success', 'Alta médica autorizada exitosamente.');
    }

    /**
     * Registrar el alta de enfermería de una hospitalización.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización con alta de enfermería
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de confirmación
     */
    public function darAltaEnfermeria(Hospitalizacion $hospitalizacion)
    {
        $hospitalizacion->update(['estado' => 'alta_enfermeria']);

        // Notificar a recepción
        $recepcionistas = User::where('role', 'recepcionista')->where('activo', true)->get();
        foreach ($recepcionistas as $recepcionista) {
            NotificacionSistema::crearNotificacion(
                Auth::id(),
                $recepcionista->id,
                'alta_enfermeria_autorizada',
                'Alta de Enfermería Autorizada',
                "El jefe de enfermería ha autorizado el alta para el paciente {$hospitalizacion->paciente->nombre_completo}. Pendiente pago.",
                [
                    'hospitalizacion_id' => $hospitalizacion->id,
                    'paciente_id' => $hospitalizacion->paciente_id
                ]
            );
        }

        return back()->with('success', 'Alta de enfermería autorizada exitosamente.');
    }

    /**
     * Completar el proceso de alta cerrando la hospitalización.
     *
     * @param \App\Models\Hospitalizacion $hospitalizacion Hospitalización finalizada
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito
     */
    public function completarAlta(Hospitalizacion $hospitalizacion)
    {
        $hospitalizacion->update([
            'estado' => 'completado',
            'fecha_egreso' => now()
        ]);

        return back()->with('success', 'Alta completada exitosamente.');
    }

    /**
     * Obtener habitaciones disponibles junto con su capacidad restante.
     *
     * Se utiliza en interfaces dinámicas para sugerir habitaciones que pueden
     * recibir nuevos pacientes.
     *
     * @return \Illuminate\Http\JsonResponse Listado de habitaciones con disponibilidad
     */
    public function habitacionesDisponibles()
    {
        $habitaciones = Habitacion::with(['hospitalizacionesActivas'])
            ->get()
            ->map(function ($habitacion) {
                $habitacion->capacidad_ocupada = $habitacion->hospitalizacionesActivas->count();
                $habitacion->capacidad_disponible = $habitacion->capacidad_maxima - $habitacion->capacidad_ocupada;
                return $habitacion;
            })
            ->filter(function ($habitacion) {
                return $habitacion->capacidad_disponible > 0;
            });

        return response()->json($habitaciones);
    }
}