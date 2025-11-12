<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Hospitalizacion;
use App\Models\Consulta;
use App\Models\Habitacion;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Auth;

class MedicoGeneralController extends Controller
{
    /**
     * Mostrar el dashboard principal del médico general.
     *
     * Presenta un panorama de pacientes hospitalizados, consultas programadas
     * para el día, historial de consultas pendientes y el número de notificaciones
     * sin leer para el profesional autenticado.
     *
     * @return \Illuminate\Contracts\View\View Vista con los indicadores del médico
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener datos para el dashboard
        $pacientesHospitalizados = Hospitalizacion::with(['paciente', 'habitacion'])
            ->where('medico_general_id', $user->id)
            ->where('estado', '!=', 'alta')
            ->get();

        // Solo consultas pendientes del día
        $consultasHoy = Consulta::with(['paciente', 'historiaClinica'])
            ->where('medico_id', $user->id)
            ->where('estado', 'pendiente')
            ->whereDate('fecha_consulta', today())
            ->get();

        // Consultas pendientes (todas, no solo del día)
        $consultasPendientes = Consulta::with(['paciente'])
            ->where('medico_id', $user->id)
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc')
            ->get();

        $notificacionesNoLeidas = $user->notificacionesNoLeidas()->count();

        return view('medico_general.dashboard', compact(
            'pacientesHospitalizados',
            'consultasHoy', 
            'consultasPendientes',
            'notificacionesNoLeidas'
        ));
    }

    /**
     * Listar los pacientes asignados al médico general autenticado.
     *
     * Filtra los pacientes por hospitalizaciones activas vinculadas al médico
     * y carga dichas hospitalizaciones para mostrar contexto clínico.
     *
     * @return \Illuminate\Contracts\View\View Vista con el listado paginado de pacientes
     */
    public function pacientes()
    {
        $user = Auth::user();
        
        $pacientes = Paciente::whereHas('hospitalizaciones', function($query) use ($user) {
            $query->where('medico_general_id', $user->id);
        })->with(['hospitalizaciones' => function($query) use ($user) {
            $query->where('medico_general_id', $user->id);
        }])->paginate(15);

        return view('medico_general.pacientes', compact('pacientes'));
    }

    /**
     * Mostrar los detalles clínicos de un paciente específico.
     *
     * Valida que el médico tenga acceso al paciente revisando la hospitalización
     * asociada y carga la historia clínica junto con las consultas previas del
     * mismo profesional.
     *
     * @param \App\Models\Paciente $paciente Paciente cuyos datos se desean revisar
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con detalles o redirección si no hay acceso
     */
    public function verPaciente(Paciente $paciente)
    {
        $user = Auth::user();
        
        // Verificar que el médico tenga acceso a este paciente
        $hospitalizacion = Hospitalizacion::where('paciente_id', $paciente->id)
            ->where('medico_general_id', $user->id)
            ->first();

        if (!$hospitalizacion) {
            return redirect()->route('medico_general.dashboard')
                ->with('error', 'No tienes acceso a este paciente.');
        }

        $paciente->load(['historiaClinica', 'consultas' => function($query) use ($user) {
            $query->where('medico_id', $user->id);
        }]);

        return view('medico_general.ver-paciente', compact('paciente', 'hospitalizacion'));
    }

    /**
     * Mostrar el formulario para registrar una nueva consulta médica.
     *
     * Verifica previamente que el paciente esté asignado al médico actual
     * mediante una hospitalización activa.
     *
     * @param \App\Models\Paciente $paciente Paciente sobre el que se creará la consulta
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista del formulario o redirección por falta de permisos
     */
    public function crearConsulta(Paciente $paciente)
    {
        $user = Auth::user();
        
        // Verificar acceso al paciente
        $hospitalizacion = Hospitalizacion::where('paciente_id', $paciente->id)
            ->where('medico_general_id', $user->id)
            ->first();

        if (!$hospitalizacion) {
            return redirect()->route('medico_general.dashboard')
                ->with('error', 'No tienes acceso a este paciente.');
        }

        return view('medico_general.crear-consulta', compact('paciente', 'hospitalizacion'));
    }

    /**
     * Persistir una nueva consulta médica realizada por el doctor general.
     *
     * Registra la consulta con diagnóstico, tratamiento y observaciones, además
     * de generar una notificación al paciente o familiar responsable.
     *
     * @param \Illuminate\Http\Request $request Datos enviados desde el formulario de consulta
     * @param \App\Models\Paciente $paciente Paciente asociado a la consulta
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito o error
     */
    public function storeConsulta(Request $request, Paciente $paciente)
    {
        $user = Auth::user();
        
        // Verificar acceso al paciente
        $hospitalizacion = Hospitalizacion::where('paciente_id', $paciente->id)
            ->where('medico_general_id', $user->id)
            ->first();

        if (!$hospitalizacion) {
            return redirect()->route('medico_general.dashboard')
                ->with('error', 'No tienes acceso a este paciente.');
        }

        $request->validate([
            'fecha_consulta' => 'required|date',
            'hora_consulta' => 'required',
            'motivo_consulta' => 'required|string|max:500',
            'diagnostico' => 'required|string|max:1000',
            'tratamiento' => 'required|string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
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
                'estado' => 'completada',
            ]);

            // Crear notificación para el paciente/familia
            NotificacionSistema::crearNotificacion(
                $user->id,
                $paciente->id,
                'consulta_completada',
                'Consulta Médica Completada',
                "Su consulta médica ha sido completada por el Dr. {$user->name}",
                ['consulta_id' => $consulta->id]
            );

            return redirect()->route('medico_general.ver-paciente', $paciente)
                ->with('success', 'Consulta creada exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear la consulta: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Listar las hospitalizaciones asignadas al médico general.
     *
     * Incluye información del paciente, habitación y el piso correspondiente
     * para ofrecer contexto al profesional.
     *
     * @return \Illuminate\Contracts\View\View Vista con hospitalizaciones paginadas
     */
    public function hospitalizaciones()
    {
        $user = Auth::user();
        
        $hospitalizaciones = Hospitalizacion::where('medico_general_id', $user->id)
            ->with(['paciente', 'habitacion.modulo.piso'])
            ->orderBy('fecha_ingreso', 'desc')
            ->paginate(15);

        return view('medico_general.hospitalizaciones', compact('hospitalizaciones'));
    }

    /**
     * Mostrar el formulario para hospitalizar a un paciente.
     *
     * Obtiene pacientes activos y habitaciones disponibles para facilitar la
     * asignación inmediata durante el proceso de internación.
     *
     * @return \Illuminate\Contracts\View\View Vista con catálogos de pacientes y habitaciones
     */
    public function crearHospitalizacion()
    {
        $pacientes = Paciente::where('activo', true)->get();
        $habitaciones = Habitacion::with(['modulo.piso'])->disponibles()->get();
        
        return view('medico_general.crear-hospitalizacion', compact('pacientes', 'habitaciones'));
    }

    /**
     * Registrar una nueva hospitalización para un paciente.
     *
     * Valida los datos ingresados, crea la hospitalización ligándola al médico
     * autenticado y marca la habitación como no disponible.
     *
     * @param \Illuminate\Http\Request $request Datos de la hospitalización enviados desde el formulario
     * @return \Illuminate\Http\RedirectResponse Redirección a la lista de hospitalizaciones con mensaje de éxito
     * @throws \Illuminate\Validation\ValidationException Si los datos proporcionados son inválidos
     */
    public function storeHospitalizacion(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'habitacion_id' => 'required|exists:habitaciones,id',
            'diagnostico_preliminar' => 'required|string',
            'fecha_ingreso' => 'required|date',
            'hora_ingreso' => 'required',
            'indicaciones_iniciales' => 'nullable|string'
        ]);

        // Crear hospitalización
        $hospitalizacion = Hospitalizacion::create([
            'paciente_id' => $validated['paciente_id'],
            'habitacion_id' => $validated['habitacion_id'],
            'medico_general_id' => Auth::id(),
            'diagnostico_inicial' => $validated['diagnostico_preliminar'],
            'fecha_ingreso' => $validated['fecha_ingreso'],
            'hora_ingreso' => $validated['hora_ingreso'],
            'observaciones' => $validated['indicaciones_iniciales'] ?? null,
            'estado' => 'activo'
        ]);

        // Actualizar disponibilidad de habitación si es necesario
        $habitacion = Habitacion::find($validated['habitacion_id']);
        if ($habitacion) {
            $habitacion->update(['disponible' => false]);
        }

        return redirect()
            ->route('medico_general.hospitalizaciones.index')
            ->with('success', 'Paciente hospitalizado exitosamente');
    }

    /**
     * Presentar la vista para atender una consulta pendiente.
     *
     * Carga la consulta con su paciente, valida que pertenezca al médico actual
     * y garantiza que aún se encuentre pendiente de atención.
     *
     * @param int $id Identificador de la consulta que se atenderá
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista de atención o redirección en caso inválido
     */
    public function atenderConsulta($id)
    {
        $consulta = \App\Models\Consulta::with('paciente')->findOrFail($id);
        
        // Verificar que la consulta sea del médico actual
        if ($consulta->medico_id !== auth()->id()) {
            abort(403, 'No tiene permiso para atender esta consulta');
        }

        // Verificar que la consulta esté pendiente
        if ($consulta->estado !== 'pendiente') {
            return redirect()->route('medico_general.dashboard')
                ->with('error', 'Esta consulta ya fue atendida.');
        }
        
        return view('medico_general.consultas.atender', compact('consulta'));
    }

    /**
     * Finalizar una consulta médica y registrar las acciones posteriores.
     *
     * Actualiza signos vitales, diagnóstico y tratamiento, además de ejecutar
     * la acción seleccionada (derivar a caja, hospitalizar o dar de alta) y
     * generar las notificaciones pertinentes.
     *
     * @param \Illuminate\Http\Request $request Información clínica y acción a ejecutar
     * @param int $id Identificador de la consulta que se finaliza
     * @return \Illuminate\Http\RedirectResponse Redirección al dashboard con mensaje descriptivo
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la consulta no existe
     */
    public function finalizarConsulta(\Illuminate\Http\Request $request, $id)
    {
        $consulta = \App\Models\Consulta::findOrFail($id);
        
        // Verificar que sea del médico actual
        if ($consulta->medico_id !== auth()->id()) {
            abort(403, 'No tiene permiso para finalizar esta consulta');
        }

        $request->validate([
            'presion_arterial' => 'nullable|string',
            'temperatura' => 'nullable|numeric',
            'frecuencia_cardiaca' => 'nullable|integer',
            'frecuencia_respiratoria' => 'nullable|integer',
            'saturacion_oxigeno' => 'nullable|integer',
            'peso' => 'nullable|numeric',
            'talla' => 'nullable|numeric',
            'diagnostico' => 'required|string',
            'tratamiento' => 'required|string',
            'accion' => 'required|in:caja,alta,hospitalizar'
        ]);

        // Actualizar consulta con signos vitales y diagnóstico
        $consulta->update([
            'presion_arterial' => $request->presion_arterial,
            'temperatura' => $request->temperatura,
            'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
            'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
            'saturacion_oxigeno' => $request->saturacion_oxigeno,
            'peso' => $request->peso,
            'talla' => $request->talla,
            'diagnostico' => $request->diagnostico,
            'tratamiento' => $request->tratamiento,
            'estado' => 'completada',
            'hora_atencion' => now()
        ]);

        // Procesar según la acción seleccionada
        if ($request->accion === 'caja') {
            \App\Models\Factura::create([
                'paciente_id' => $consulta->paciente_id,
                'consulta_id' => $consulta->id,
                'numero_factura' => 'FAC-' . str_pad($consulta->id, 8, '0', STR_PAD_LEFT),
                'subtotal' => 1000,
                'impuestos' => 0,
                'total' => 1000,
                'metodo_pago' => 'efectivo', // Valor por defecto, se actualizará en caja
                'estado' => 'pendiente',
                'fecha_emision' => now()
            ]);
            
            $usuariosCaja = \App\Models\User::where('role', 'caja')->where('activo', 1)->get();
            foreach ($usuariosCaja as $caja) {
                \App\Models\NotificacionSistema::create([
                    'usuario_emisor_id' => auth()->id(),
                    'usuario_receptor_id' => $caja->id,
                    'titulo' => 'Paciente derivado a Caja',
                    'mensaje' => 'Paciente: ' . $consulta->paciente->nombres . ' ' . $consulta->paciente->apellidos . ' debe realizar pago de $1000',
                    'tipo' => 'derivacion_caja',
                    'leida' => false
                ]);
            }
            
            $mensaje = 'Consulta finalizada. Paciente derivado a Caja para procesar pago.';
            
        } elseif ($request->accion === 'hospitalizar') {
            // Notificar a jefes de enfermería para coordinar la internación
            $jefesEnfermeria = \App\Models\User::where('role', 'jefe_enfermeria')->where('activo', 1)->get();
            foreach ($jefesEnfermeria as $jefe) {
                \App\Models\NotificacionSistema::create([
                    'usuario_emisor_id' => auth()->id(),
                    'usuario_receptor_id' => $jefe->id,
                    'titulo' => 'Paciente para hospitalizar',
                    'mensaje' => 'Paciente: ' . $consulta->paciente->nombres . ' ' . $consulta->paciente->apellidos . ' requiere internación',
                    'tipo' => 'hospitalizacion',
                    'leida' => false
                ]);
            }
            
            $mensaje = 'Consulta finalizada. Paciente derivado a hospitalización.';
        } else {
            // En caso de alta, solo se informa la finalización de la consulta
            $mensaje = 'Consulta finalizada. Paciente dado de alta.';
        }

        return redirect()->route('medico_general.dashboard')->with('success', $mensaje);
    }
}