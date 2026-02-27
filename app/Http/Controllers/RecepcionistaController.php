<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\HistoriaClinica;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RecepcionistaController extends Controller
{
    /**
     * Mostrar el panel principal de la recepción.
     *
     * Reúne métricas del día como pacientes registrados, hospitalizaciones
     * pendientes y facturas por cobrar, además de listar pacientes listos para
     * salida y notificaciones recientes para el usuario autenticado.
     *
     * @return \Illuminate\Contracts\View\View Vista con el resumen operativo de recepción
     */
    public function dashboard()
    {
        // Pacientes registrados hoy
        $pacientesHoy = Paciente::whereDate('created_at', today())->count();
        
        // Hospitalizaciones pendientes de alta
        $hospitalizacionesPendientes = \App\Models\Hospitalizacion::where('estado', 'alta_enfermeria')->count();
        
        // Facturas pendientes de pago
        $facturasPendientes = \App\Models\Factura::where('estado', 'pendiente')->count();
        
        // Encuestas pendientes
        $encuestasPendientes = 0; // Se puede implementar lógica específica
        
        // Pacientes listos para salida (optimizado para evitar N+1)
        $pacientesListosParaSalida = \App\Models\Factura::where('estado', 'pagado')
            ->with(['paciente', 'consulta.encuestaSatisfaccion'])
            ->whereHas('consulta', function($q) {
                $q->where('estado', 'completada');
            })
            ->whereDate('fecha_pago', '>=', now()->subDays(7))
            ->orderBy('fecha_pago', 'desc')
            ->get()
            ->filter(function($factura) {
                return $factura->consulta && !$factura->consulta->encuestaSatisfaccion;
            });
        
        // Notificaciones
        $notificaciones = \App\Models\NotificacionSistema::where('usuario_receptor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.recepcion', compact(
            'pacientesHoy',
            'hospitalizacionesPendientes',
            'facturasPendientes',
            'encuestasPendientes',
            'pacientesListosParaSalida',
            'notificaciones'
        ));
    }

    /**
     * Listar pacientes registrados para gestión desde recepción.
     *
     * Permite aplicar filtros de búsqueda, sexo y ciudad, además de excluír
     * pacientes con consultas pendientes para evitar duplicidad en derivaciones.
     *
     * @param \Illuminate\Http\Request $request Parámetros de filtrado ingresados en la vista
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista paginada de pacientes o redirección con error
     */
    public function index(Request $request)
    {
        try {
            $pacientes = \App\Models\Paciente::with(['consultas' => function($q) {
                    $q->whereIn('estado', ['pendiente', 'completada'])
                      ->latest();
                }])
                ->when($request->search, function ($query, $search) {
                    return $query->where(function($q) use ($search) {
                        $q->where('dni', 'like', "%{$search}%")
                          ->orWhere('nombres', 'like', "%{$search}%")
                          ->orWhere('apellidos', 'like', "%{$search}%");
                    });
                })
                ->when($request->sexo && $request->sexo !== 'Todos', function ($query, $sexo) {
                    return $query->where('sexo', $sexo);
                })
                ->when($request->ciudad, function ($query, $ciudad) {
                    return $query->where('ciudad', 'like', "%{$ciudad}%");
                })
                ->whereDoesntHave('consultas', function($q) {
                    $q->where('estado', 'pendiente');
                })
                ->paginate(10);
            
            return view('recepcion.pacientes.index', compact('pacientes'));
            
        } catch (\Exception $e) {
            Log::error('Error al listar pacientes: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al cargar la lista de pacientes.']);
        }
    }

    /**
     * Mostrar el formulario de registro de un nuevo paciente.
     *
     * @return \Illuminate\Contracts\View\View Vista con el formulario en blanco
     */
    public function crear()
    {
        return view('recepcion.pacientes.crear');
    }

    /**
     * Registrar un nuevo paciente en el sistema.
     *
     * Valida los datos proporcionados, maneja la carga de la fotografía, crea la
     * historia clínica asociada y registra la acción en el log para auditoría.
     *
     * @param \Illuminate\Http\Request $request Información recopilada en el formulario de registro
     * @return \Illuminate\Http\RedirectResponse Redirección al listado con mensaje de éxito o retorno con error
     * @throws \Illuminate\Validation\ValidationException Si los datos no cumplen las reglas definidas
     */
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|unique:pacientes,dni|max:20|regex:/^[0-9]{7,8}$/',
            'nombres' => 'required|string|max:255|min:2',
            'apellidos' => 'required|string|max:255|min:2',
            'fecha_nacimiento' => 'required|date|before:today|after:1900-01-01',
            'sexo' => 'required|in:M,F,Otro',
            'telefono' => 'required|string|max:20|regex:/^[0-9+\-\s()]{8,20}$/',
            'email' => 'required|email|max:255',
            'direccion' => 'required|string|max:500',
            'ciudad' => 'required|string|max:255',
            'tipo_sangre' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'alergias' => 'nullable|string|max:1000',
            'enfermedades_cronicas' => 'nullable|string|max:1000',
            'contacto_emergencia_nombre' => 'required|string|max:255|min:2',
            'contacto_emergencia_telefono' => 'required|string|max:20|regex:/^[0-9+\-\s()]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Ya existe un paciente con este DNI.',
            'dni.regex' => 'El DNI debe contener entre 7 y 8 dígitos.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'nombres.min' => 'Los nombres deben tener al menos 2 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.min' => 'Los apellidos deben tener al menos 2 caracteres.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_nacimiento.after' => 'La fecha de nacimiento debe ser posterior a 1900.',
            'sexo.required' => 'El sexo es obligatorio.',
            'sexo.in' => 'El sexo debe ser Masculino, Femenino u Otro.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe contener entre 8 y 20 caracteres numéricos.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'direccion.required' => 'La dirección es obligatoria.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'tipo_sangre.in' => 'El tipo de sangre debe ser uno de los valores válidos.',
            'contacto_emergencia_nombre.required' => 'El nombre del contacto de emergencia es obligatorio.',
            'contacto_emergencia_telefono.required' => 'El teléfono del contacto de emergencia es obligatorio.',
            'contacto_emergencia_telefono.regex' => 'El teléfono del contacto de emergencia debe contener entre 8 y 20 caracteres numéricos.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La imagen debe ser JPEG, PNG, JPG o GIF.',
            'foto.max' => 'La imagen no debe superar los 2MB.',
        ]);

        try {
            $pacienteData = $request->only([
                'dni', 'nombres', 'apellidos', 'fecha_nacimiento', 'sexo',
                'telefono', 'email', 'direccion', 'ciudad', 'tipo_sangre',
                'alergias', 'enfermedades_cronicas',
                'contacto_emergencia_nombre', 'contacto_emergencia_telefono'
            ]);

            // Manejar subida de foto
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('pacientes', 'public');
                $pacienteData['foto'] = $fotoPath;
            }

            // Crear paciente
            $paciente = Paciente::create($pacienteData);
            
            // Crear historia clínica automáticamente
            $paciente->historiaClinica()->create([]);
            
            // Log de auditoría
            Log::info('Paciente creado', [
                'paciente_id' => $paciente->id,
                'dni' => $paciente->dni,
                'nombre' => $paciente->nombre_completo,
                'creado_por' => Auth::user()->name,
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('recepcion.pacientes.index')
                ->with('success', "Paciente {$paciente->nombre_completo} creado exitosamente.");
                
        } catch (\Exception $e) {
            Log::error('Error al crear paciente: ' . $e->getMessage(), [
                'request_data' => $request->except(['foto']),
                'user_id' => Auth::id()
            ]);
            
            return back()->withErrors(['error' => 'Error al crear paciente. Por favor, intente nuevamente.'])
                ->withInput();
        }
    }

    /**
     * Mostrar la ficha detallada de un paciente.
     *
     * Carga historia clínica, citas y consultas con su respectivo médico para
     * brindar un panorama completo al personal de recepción.
     *
     * @param \App\Models\Paciente $paciente Paciente que se va a consultar
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con datos completos o redirección en caso de error
     */
    public function show(Paciente $paciente)
    {
        try {
            $paciente->load(['historiaClinica', 'citas.medico', 'consultas.medico']);
            
            return view('recepcion.pacientes.ver', compact('paciente'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar paciente: ' . $e->getMessage(), [
                'paciente_id' => $paciente->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Error al cargar los datos del paciente.']);
        }
    }

    /**
     * Mostrar el formulario de edición de un paciente específico.
     *
     * @param \App\Models\Paciente $paciente Paciente cuyos datos se editarán
     * @return \Illuminate\Contracts\View\View Vista con el formulario precargado
     */
    public function edit(Paciente $paciente)
    {
        return view('recepcion.pacientes.editar', compact('paciente'));
    }

    /**
     * Actualizar los datos de un paciente previamente registrado.
     *
     * Valida la información, gestiona el reemplazo de la fotografía en disco
     * y registra la actividad para fines de auditoría.
     *
     * @param \Illuminate\Http\Request $request Datos actualizados enviados desde el formulario
     * @param \App\Models\Paciente $paciente Paciente que se desea modificar
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de confirmación o error
     * @throws \Illuminate\Validation\ValidationException Si los datos no cumplen las reglas de validación
     */
    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'dni' => 'required|max:20|unique:pacientes,dni,' . $paciente->id . '|regex:/^[0-9]{7,8}$/',
            'nombres' => 'required|string|max:255|min:2',
            'apellidos' => 'required|string|max:255|min:2',
            'fecha_nacimiento' => 'required|date|before:today|after:1900-01-01',
            'sexo' => 'required|in:M,F,Otro',
            'telefono' => 'required|string|max:20|regex:/^[0-9+\-\s()]{8,20}$/',
            'email' => 'required|email|max:255',
            'direccion' => 'required|string|max:500',
            'ciudad' => 'required|string|max:255',
            'tipo_sangre' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'alergias' => 'nullable|string|max:1000',
            'enfermedades_cronicas' => 'nullable|string|max:1000',
            'contacto_emergencia_nombre' => 'required|string|max:255|min:2',
            'contacto_emergencia_telefono' => 'required|string|max:20|regex:/^[0-9+\-\s()]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Ya existe otro paciente con este DNI.',
            'dni.regex' => 'El DNI debe contener entre 7 y 8 dígitos.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'nombres.min' => 'Los nombres deben tener al menos 2 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.min' => 'Los apellidos deben tener al menos 2 caracteres.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_nacimiento.after' => 'La fecha de nacimiento debe ser posterior a 1900.',
            'sexo.required' => 'El sexo es obligatorio.',
            'sexo.in' => 'El sexo debe ser Masculino, Femenino u Otro.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe contener entre 8 y 20 caracteres numéricos.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'direccion.required' => 'La dirección es obligatoria.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'tipo_sangre.in' => 'El tipo de sangre debe ser uno de los valores válidos.',
            'contacto_emergencia_nombre.required' => 'El nombre del contacto de emergencia es obligatorio.',
            'contacto_emergencia_telefono.required' => 'El teléfono del contacto de emergencia es obligatorio.',
            'contacto_emergencia_telefono.regex' => 'El teléfono del contacto de emergencia debe contener entre 8 y 20 caracteres numéricos.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La imagen debe ser JPEG, PNG, JPG o GIF.',
            'foto.max' => 'La imagen no debe superar los 2MB.',
        ]);

        try {
            $pacienteData = $request->only([
                'dni', 'nombres', 'apellidos', 'fecha_nacimiento', 'sexo',
                'telefono', 'email', 'direccion', 'ciudad', 'tipo_sangre',
                'alergias', 'enfermedades_cronicas',
                'contacto_emergencia_nombre', 'contacto_emergencia_telefono'
            ]);

            // Manejar subida de nueva foto
            if ($request->hasFile('foto')) {
                // Eliminar foto anterior si existe
                if ($paciente->foto && Storage::disk('public')->exists($paciente->foto)) {
                    Storage::disk('public')->delete($paciente->foto);
                }

                $fotoPath = $request->file('foto')->store('pacientes', 'public');
                $pacienteData['foto'] = $fotoPath;
            }

            // Actualizar paciente
            $paciente->update($pacienteData);
            
            // Log de auditoría
            Log::info('Paciente actualizado', [
                'paciente_id' => $paciente->id,
                'dni' => $paciente->dni,
                'nombre' => $paciente->nombre_completo,
                'actualizado_por' => Auth::user()->name,
                'ip' => $request->ip(),
                'cambios' => $request->except(['foto', '_token', '_method'])
            ]);
            
            return redirect()->route('recepcion.pacientes.index')
                ->with('success', "Paciente {$paciente->nombre_completo} actualizado exitosamente.");
                
        } catch (\Exception $e) {
            Log::error('Error al actualizar paciente: ' . $e->getMessage(), [
                'paciente_id' => $paciente->id,
                'request_data' => $request->except(['foto']),
                'user_id' => Auth::id()
            ]);
            
            return back()->withErrors(['error' => 'Error al actualizar paciente. Por favor, intente nuevamente.'])
                ->withInput();
        }
    }

    /**
     * Eliminar lógicamente un paciente del sistema.
     *
     * Verifica que no existan citas activas ni consultas registradas para
     * evitar inconsistencias, y deja constancia de la acción en el log.
     *
     * @param \App\Models\Paciente $paciente Paciente que se desea desactivar
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de resultado del proceso
     */
    public function destroy(Paciente $paciente)
    {
        try {
            // Verificar si el paciente tiene citas o consultas activas
            if ($paciente->citas()->where('estado', '!=', 'cancelada')->exists()) {
                return redirect()->back()->withErrors([
                    'error' => 'No se puede eliminar el paciente porque tiene citas activas.'
                ]);
            }
            
            if ($paciente->consultas()->exists()) {
                return redirect()->back()->withErrors([
                    'error' => 'No se puede eliminar el paciente porque tiene consultas registradas.'
                ]);
            }
            
            // Realizar soft delete
            $paciente->delete();
            
            // Log de auditoría
            Log::info('Paciente eliminado', [
                'paciente_id' => $paciente->id,
                'dni' => $paciente->dni,
                'nombre' => $paciente->nombre_completo,
                'eliminado_por' => Auth::user()->name,
                'ip' => request()->ip()
            ]);
            
            return redirect()->route('recepcion.pacientes.index')
                ->with('success', "Paciente {$paciente->nombre_completo} eliminado exitosamente.");
                
        } catch (\Exception $e) {
            Log::error('Error al eliminar paciente: ' . $e->getMessage(), [
                'paciente_id' => $paciente->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Error al eliminar paciente. Por favor, intente nuevamente.']);
        }
    }

    /**
     * Buscar un paciente por DNI vía solicitud AJAX.
     *
     * Permite a la recepción validar si un paciente ya existe sin recargar la
     * página, devolviendo información clave para su identificación.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud con el DNI a consultar
     * @return \Illuminate\Http\JsonResponse Respuesta con información del paciente o mensaje de error
     */
    public function buscarPorDni(Request $request)
    {
        try {
            $validated = $request->validate([
                'dni' => 'required|string|max:20|regex:/^[0-9]{7,8}$/'
            ]);

            $paciente = Paciente::where('dni', $validated['dni'])->first();
            
            if (!$paciente) {
                return response()->json(['error' => 'Paciente no encontrado'], 404);
            }
            
            return response()->json([
                'paciente' => [
                    'id' => $paciente->id,
                    'dni' => $paciente->dni,
                    'nombre_completo' => $paciente->nombre_completo,
                    'telefono' => $paciente->telefono,
                    'email' => $paciente->email,
                    'edad' => $paciente->edad,
                    'tipo_sangre' => $paciente->tipo_sangre,
                    'alergias' => $paciente->alergias,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al buscar paciente por DNI: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Mostrar el formulario para derivar a un paciente hacia un médico general.
     *
     * @param int $id Identificador del paciente que será derivado
     * @return \Illuminate\Contracts\View\View Vista con paciente y médicos disponibles
     */
    public function derivarPaciente($id)
    {
        $paciente = \App\Models\Paciente::findOrFail($id);
        $medicosGenerales = \App\Models\User::where('role', 'medico_general')
            ->where('activo', 1)
            ->get();
        
        return view('recepcion.pacientes.derivar', compact('paciente', 'medicosGenerales'));
    }

    /**
     * Crear una consulta general al derivar un paciente a un médico.
     *
     * Registra la consulta en estado pendiente e informa al médico asignado
     * mediante una notificación interna.
     *
     * @param \Illuminate\Http\Request $request Datos de derivación enviados desde el formulario
     * @return \Illuminate\Http\RedirectResponse Redirección al dashboard con confirmación
     * @throws \Illuminate\Validation\ValidationException Si los datos requeridos no son válidos
     */
    public function crearConsulta(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:users,id',
            'motivo_consulta' => 'required|string|max:500'
        ]);

        $paciente = \App\Models\Paciente::findOrFail($request->paciente_id);

        // Obtener o crear historia clínica (requerida NOT NULL en tabla consultas)
        $historiaClinica = $paciente->historiaClinica ?? $paciente->historiaClinica()->create([]);

        $consulta = \App\Models\Consulta::create([
            'historia_clinica_id' => $historiaClinica->id,
            'paciente_id' => $request->paciente_id,
            'medico_id' => $request->medico_id,
            'motivo' => $request->motivo_consulta,
            'motivo_consulta' => $request->motivo_consulta,
            'observaciones' => $request->observaciones,
            'fecha_consulta' => now(),
            'estado' => 'pendiente',
            'tipo_consulta' => 'general'
        ]);

        \App\Models\NotificacionSistema::create([
            'usuario_emisor_id' => Auth::id(),
            'usuario_receptor_id' => $request->medico_id,
            'titulo' => 'Nueva consulta asignada',
            'mensaje' => 'Tiene una nueva consulta del paciente: ' . $consulta->paciente->nombres . ' ' . $consulta->paciente->apellidos,
            'tipo' => 'nueva_consulta',
            'leida' => false
        ]);

        return redirect()->route('recepcion.dashboard')->with('success', 'Paciente derivado exitosamente al médico');
    }

    /**
     * Mostrar los pacientes que están listos para completar el proceso de salida.
     *
     * Filtra a aquellos con facturas pagadas y consultas completadas que aún no
     * han respondido la encuesta de satisfacción.
     *
     * @return \Illuminate\Contracts\View\View Vista con el listado de salidas pendientes
     */
    public function salidas()
    {
        $pacientesListosParaSalida = \App\Models\Factura::where('estado', 'pagado')
            ->whereHas('consulta', function($q) {
                $q->where('estado', 'completada');
            })
            ->whereDoesntHave('consulta.encuestaSatisfaccion')
            ->with(['paciente', 'consulta'])
            ->whereDate('fecha_pago', '>=', now()->subDays(7))
            ->orderBy('fecha_pago', 'desc')
            ->get();
        
        return view('recepcion.salidas', compact('pacientesListosParaSalida'));
    }

    /**
     * Iniciar el proceso de salida para un paciente específico.
     *
     * Recupera la última factura pagada asociada a una consulta completada para
     * presentar la información necesaria en la vista de salida.
     *
     * @param int $id Identificador del paciente en proceso de salida
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Vista con datos del proceso o redirección si falta información
     */
    public function procesarSalida($id)
    {
        $paciente = \App\Models\Paciente::findOrFail($id);
        
        $factura = \App\Models\Factura::where('paciente_id', $paciente->id)
            ->where('estado', 'pagado')
            ->with('consulta')
            ->latest()
            ->first();
        
        if (!$factura) {
            return redirect()->route('recepcion.salidas')->withErrors(['error' => 'No se encontró factura pagada para este paciente']);
        }
        
        return view('recepcion.pacientes.salida', compact('paciente', 'factura'));
    }

    /**
     * Confirmar la salida de un paciente registrando su encuesta de satisfacción.
     *
     * Captura los indicadores de atención, calcula el promedio general y marca
     * el cierre del proceso de salida.
     *
     * @param \Illuminate\Http\Request $request Respuestas de la encuesta y observaciones finales
     * @param int $id Identificador del paciente que egresa
     * @return \Illuminate\Http\RedirectResponse Redirección al listado de salidas con mensaje de éxito
     */
    public function confirmarSalida(\Illuminate\Http\Request $request, $id)
    {
        $paciente = \App\Models\Paciente::findOrFail($id);
        
        $consulta = \App\Models\Consulta::where('paciente_id', $paciente->id)
            ->where('estado', 'completada')
            ->latest()
            ->first();
        
        if ($consulta) {
            // Validar datos de la encuesta
            $request->validate([
                'atencion_medica' => 'required|integer|min:1|max:5',
                'tiempo_espera' => 'required|integer|min:1|max:5',
                'trato_personal' => 'required|integer|min:1|max:5',
                'comentarios_encuesta' => 'nullable|string',
                'observaciones_salida' => 'nullable|string'
            ]);

            // Calcular calidad general solo si todos los valores están presentes
            $calidad = null;
            if ($request->atencion_medica && $request->tiempo_espera && $request->trato_personal) {
                $calidad = round(($request->atencion_medica + $request->tiempo_espera + $request->trato_personal) / 3, 1);
            }

            // Guardar encuesta de satisfacción
            \App\Models\EncuestaSatisfaccion::create([
                'paciente_id' => $paciente->id,
                'consulta_id' => $consulta->id,
                'recepcion_id' => Auth::id(),
                'atencion_medica' => $request->atencion_medica,
                'atencion_enfermeria' => null,
                'limpieza_habitacion' => null,
                'comida' => null,
                'personal_recepcion' => $request->trato_personal,
                'tiempo_espera' => $request->tiempo_espera,
                'calidad_general' => $calidad,
                'comentarios' => $request->comentarios_encuesta,
                'recomendaria' => 1,
                'fecha_encuesta' => now()
            ]);
        }

        return redirect()->route('recepcion.salidas')->with('success', 'Salida procesada exitosamente');
    }
}