<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\EncuestaSatisfaccion;
use App\Models\Hospitalizacion;
use App\Models\Factura;
use App\Models\ModuloEnfermeria;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Mostrar el dashboard principal del administrador.
     *
     * Reúne indicadores globales del sistema como usuarios activos,
     * pacientes registrados, hospitalizaciones en curso e ingresos mensuales,
     * además de un balance de personal y últimos usuarios creados.
     *
     * @return \Illuminate\Contracts\View\View Vista con el resumen ejecutivo del sistema
     */
    public function index()
    {
        // Mensaje motivacional aleatorio
        $mensajes = [
            'Tu liderazgo hace la diferencia en Clínica Eden 💚',
            'Juntos construimos un mejor sistema de salud ⚕️',
            'La excelencia médica comienza con tu gestión 🌟',
            'Cada decisión tuya mejora la vida de nuestros pacientes 💙',
            'Tu compromiso inspira a todo el equipo médico ✨',
            'Gracias por dedicarte a la salud de nuestra comunidad 🏥',
        ];
        $mensajeMotivacional = $mensajes[array_rand($mensajes)];

        // Estadísticas principales
        $totalUsuarios        = User::count();
        $usuariosActivos      = User::activos()->count();
        $medicosTotal         = User::whereIn('role', ['medico_general', 'medico_especialista'])
                                    ->activos()->count();
        $pacientesTotal       = Paciente::count();
        $hospitalizadosActuales = Hospitalizacion::activas()->count();

        // Ingresos del mes actual
        $ingresosMes = Factura::where('estado', 'pagado')
            ->whereMonth('fecha_emision', now()->month)
            ->whereYear('fecha_emision', now()->year)
            ->sum('total');

        // Actividad reciente: últimos usuarios + últimos pacientes registrados (24 h)
        $actividadReciente = collect();

        User::where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($u) use (&$actividadReciente) {
                $actividadReciente->push((object)[
                    'icono'       => 'fa-user-plus',
                    'color'       => '#2D5F8A',
                    'descripcion' => "Nuevo usuario registrado: {$u->name}",
                    'detalle'     => ucfirst(str_replace('_', ' ', $u->role)),
                    'tiempo'      => $u->created_at,
                ]);
            });

        Paciente::where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($p) use (&$actividadReciente) {
                $actividadReciente->push((object)[
                    'icono'       => 'fa-user-injured',
                    'color'       => '#27AE60',
                    'descripcion' => "Paciente registrado: {$p->nombres} {$p->apellidos}",
                    'detalle'     => "DNI: {$p->dni}",
                    'tiempo'      => $p->created_at,
                ]);
            });

        $actividadReciente = $actividadReciente
            ->sortByDesc('tiempo')
            ->take(6)
            ->values();

        return view('admin.dashboard', compact(
            'mensajeMotivacional',
            'totalUsuarios',
            'usuariosActivos',
            'medicosTotal',
            'pacientesTotal',
            'hospitalizadosActuales',
            'ingresosMes',
            'actividadReciente'
        ));
    }

    /**
     * Listar todos los usuarios administrados por el sistema.
     *
     * Permite filtrar por rol, estado activo e incluye búsqueda por datos
     * personales. El resultado se pagina para facilitar su navegación.
     *
     * @param \Illuminate\Http\Request $request Datos de filtrado ingresados por el administrador
     * @return \Illuminate\Contracts\View\View Vista con el listado de usuarios filtrados y paginados
     */
    public function usuarios(Request $request)
    {
        $query = User::query();

        // Filtros
        if ($request->filled('rol')) {
            $query->where('role', $request->rol);
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        if ($request->filled('search')) {
            // Agrupar condiciones de búsqueda para abarcar múltiples campos
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('apellido', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('dni', 'like', '%' . $request->search . '%');
            });
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = User::select('role')->distinct()->pluck('role');
        
        $usuariosInactivos = User::where('activo', 0)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'usuariosInactivos'));
    }

    /**
     * Mostrar el formulario de creación de usuarios.
     *
     * Carga los catálogos de roles y especialidades disponibles para facilitar
     * la asignación de funciones y áreas médicas.
     *
     * @return \Illuminate\Contracts\View\View Vista del formulario de creación de usuarios
     */
    public function crearUsuario()
    {
        $roles = [
            'admin' => 'Administrador',
            'recepcionista' => 'Recepcionista',
            'medico_general' => 'Médico General',
            'medico_especialista' => 'Médico Especialista',
            'jefe_enfermeria' => 'Jefe de Enfermería',
            'auxiliar_enfermeria' => 'Auxiliar de Enfermería',
            'caja' => 'Caja'
        ];

        $especialidades = [
            'Cardiología',
            'Neurología',
            'Pediatría',
            'Ginecología',
            'Traumatología',
            'Dermatología',
            'Psiquiatría',
            'Oftalmología',
            'Otorrinolaringología',
            'Urología'
        ];

        return view('admin.usuarios.crear', compact('roles', 'especialidades'));
    }

    /**
     * Persistir un nuevo usuario en la plataforma.
     *
     * Valida los datos de entrada, gestiona la subida de archivos (foto y diploma)
     * y crea el registro del usuario con la contraseña encriptada.
     *
     * @param \Illuminate\Http\Request $request Información del nuevo usuario enviada desde el formulario
     * @return \Illuminate\Http\RedirectResponse Redirección al listado con mensaje de éxito
     * @throws \Illuminate\Validation\ValidationException Si la validación de datos falla
     */
    public function guardarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:users,dni',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'foto' => 'nullable|image|max:2048',
            'diploma' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        $data = $request->except(['foto', 'diploma']);
        $data['password'] = bcrypt($request->password);
        
        if ($request->hasFile('foto')) {
            $data['profile_photo_path'] = $request->file('foto')->store('fotos', 'public');
        }
        
        if ($request->hasFile('diploma')) {
            $data['diploma_path'] = $request->file('diploma')->store('diplomas', 'public');
        }

        User::create($data);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Mostrar los detalles completos de un usuario.
     *
     * Incluye la carga de relaciones para visualizar asignaciones dentro de los
     * módulos de enfermería.
     *
     * @param \App\Models\User $usuario Usuario que se desea visualizar
     * @return \Illuminate\Contracts\View\View Vista con la información detallada del usuario
     */
    public function verUsuario(User $usuario)
    {
        $usuario->load(['modulosComoJefe', 'modulosComoAuxiliar']);
        return view('admin.usuarios.ver', compact('usuario'));
    }

    /**
     * Mostrar el formulario de edición de un usuario.
     *
     * Prepara los catálogos de roles y especialidades para actualizar la
     * información del usuario seleccionado.
     *
     * @param \App\Models\User $usuario Usuario que se desea editar
     * @return \Illuminate\Contracts\View\View Vista con el formulario y datos del usuario
     */
    public function editarUsuario(User $usuario)
    {
        $roles = [
            'admin' => 'Administrador',
            'recepcionista' => 'Recepcionista',
            'medico_general' => 'Médico General',
            'medico_especialista' => 'Médico Especialista',
            'jefe_enfermeria' => 'Jefe de Enfermería',
            'auxiliar_enfermeria' => 'Auxiliar de Enfermería',
            'caja' => 'Caja'
        ];

        $especialidades = [
            'Cardiología',
            'Neurología',
            'Pediatría',
            'Ginecología',
            'Traumatología',
            'Dermatología',
            'Psiquiatría',
            'Oftalmología',
            'Otorrinolaringología',
            'Urología'
        ];

        return view('admin.usuarios.editar', compact('usuario', 'roles', 'especialidades'));
    }

    /**
     * Actualizar los datos de un usuario existente.
     *
     * Valida la información recibida, gestiona la actualización de archivos
     * opcionales y persiste los cambios en el modelo correspondiente.
     *
     * @param \Illuminate\Http\Request $request Datos actualizados enviados desde el formulario
     * @param \App\Models\User $usuario Usuario que se modificará
     * @return \Illuminate\Http\RedirectResponse Redirección al listado con confirmación de la actualización
     * @throws \Illuminate\Validation\ValidationException Si la validación de datos falla
     */
    public function actualizarUsuario(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:users,dni,' . $usuario->id,
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:8',
            'foto' => 'nullable|image|max:2048',
            'diploma' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        $data = $request->except(['foto', 'diploma', 'password']);
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        
        if ($request->hasFile('foto')) {
            $data['profile_photo_path'] = $request->file('foto')->store('fotos', 'public');
        }
        
        if ($request->hasFile('diploma')) {
            $data['diploma_path'] = $request->file('diploma')->store('diplomas', 'public');
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Desactivar (eliminar lógicamente) un usuario del sistema.
     *
     * Marca al usuario como inactivo y registra el evento en el log para fines
     * de auditoría y trazabilidad.
     *
     * @param \App\Models\User $usuario Usuario que se desea desactivar
     * @return \Illuminate\Http\RedirectResponse Redirección al listado con notificación de desactivación
     */
    public function eliminarUsuario(User $usuario)
    {
        $usuario->update(['activo' => 0]);
        
        Log::info('Usuario desactivado', [
            'usuario_id' => $usuario->id,
            'nombre' => $usuario->name,
            'email' => $usuario->email,
            'desactivado_por' => Auth::user()->name
        ]);
        
        return redirect()->route('admin.usuarios')->with('success', 'Usuario desactivado exitosamente');
    }

    /**
     * Mostrar la vista de asignación de médicos a los módulos disponibles.
     *
     * Recupera médicos activos con especialidad y módulos habilitados para
     * facilitar la gestión de asignaciones.
     *
     * @return \Illuminate\Contracts\View\View Vista con listados de médicos y módulos
     */
    public function asignarMedicos()
    {
        $medicos = User::medicosConEspecialidad()->activos()->get();
        $modulos = ModuloEnfermeria::with(['jefeEnfermeria'])->activos()->get();
        
        return view('admin.asignaciones.medicos', compact('medicos', 'modulos'));
    }

    /**
     * Mostrar la vista de asignación de jefes de enfermería a módulos.
     *
     * Proporciona los jefes disponibles y los módulos activos para coordinar la
     * supervisión de personal.
     *
     * @return \Illuminate\Contracts\View\View Vista de asignaciones de jefaturas
     */
    public function asignarJefesEnfermeria()
    {
        $jefes = User::porRol('jefe_enfermeria')->activos()->get();
        $modulos = ModuloEnfermeria::with(['jefeEnfermeria'])->activos()->get();
        
        return view('admin.asignaciones.jefes-enfermeria', compact('jefes', 'modulos'));
    }

    /**
     * Mostrar la vista de asignación de auxiliares de enfermería.
     *
     * Permite visualizar los auxiliares activos y los módulos para distribuir
     * el personal de apoyo.
     *
     * @return \Illuminate\Contracts\View\View Vista con opciones de auxiliares y módulos
     */
    public function asignarAuxiliares()
    {
        $auxiliares = User::porRol('auxiliar_enfermeria')->activos()->get();
        $modulos = ModuloEnfermeria::with(['auxiliares'])->activos()->get();
        
        return view('admin.asignaciones.auxiliares', compact('auxiliares', 'modulos'));
    }

    /**
     * Mostrar reportes e indicadores clave del sistema.
     *
     * Calcula estadísticas de usuarios por rol, ingresos pagados en los últimos
     * doce meses y el flujo de registro de pacientes.
     *
     * @return \Illuminate\Contracts\View\View Vista con los reportes consolidados
     */
    public function reportes()
    {
        // Estadísticas de usuarios por rol
        $usuariosPorRol = User::activos()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->get();

        // Ingresos por mes (últimos 12 meses)
        $ingresosPorMes = Factura::where('estado', 'pagado')
            ->where('fecha_emision', '>=', now()->subMonths(12))
            ->selectRaw('YEAR(fecha_emision) as año, MONTH(fecha_emision) as mes, SUM(total) as total')
            ->groupBy('año', 'mes')
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        // Pacientes por mes
        $pacientesPorMes = Paciente::where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('YEAR(created_at) as año, MONTH(created_at) as mes, COUNT(*) as total')
            ->groupBy('año', 'mes')
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return view('admin.reportes', compact(
            'usuariosPorRol',
            'ingresosPorMes',
            'pacientesPorMes'
        ));
    }

    /**
     * Presentar el balance general del personal por rol y módulo.
     *
     * Agrupa el personal activo por rol y detalla la asignación en cada módulo,
     * incluyendo cantidad y nombres de auxiliares asignados.
     *
     * @return \Illuminate\Contracts\View\View Vista con el balance de personal por módulo
     */
    public function balancePersonal()
    {
        $balancePersonal = User::activos()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->get()
            ->pluck('total', 'role');

        $modulosConPersonal = ModuloEnfermeria::with(['jefeEnfermeria', 'auxiliares'])
            ->activos()
            ->get();

        $personalPorModulo = [];
        foreach ($modulosConPersonal as $modulo) {
            // Construir resumen legible del personal asignado al módulo
            $personalPorModulo[] = [
                'modulo' => $modulo->nombre,
                'piso' => $modulo->piso->numero,
                'jefe' => $modulo->jefeEnfermeria ? $modulo->jefeEnfermeria->getNombreCompletoAttribute() : 'Sin asignar',
                'auxiliares' => $modulo->auxiliares->count(),
                'auxiliares_nombres' => $modulo->auxiliares->map(function($auxiliar) {
                    return $auxiliar->getNombreCompletoAttribute();
                })->toArray()
            ];
        }

        return view('admin.balance-personal', compact('balancePersonal', 'personalPorModulo'));
    }

    /**
     * Mostrar el balance de ingresos financieros del sistema.
     *
     * Compara ingresos del mes actual y anterior, consolida totales anuales y
     * resume el estado de facturas pagadas y pendientes.
     *
     * @return \Illuminate\Contracts\View\View Vista con métricas financieras clave
     */
    public function balanceIngresos()
    {
        // Ingresos del mes actual
        $ingresosMesActual = Factura::whereMonth('fecha_emision', now()->month)
            ->whereYear('fecha_emision', now()->year)
            ->where('estado', 'pagado')
            ->sum('total');

        // Ingresos del mes anterior
        $ingresosMesAnterior = Factura::whereMonth('fecha_emision', now()->subMonth()->month)
            ->whereYear('fecha_emision', now()->subMonth()->year)
            ->where('estado', 'pagado')
            ->sum('total');

        // Ingresos por año
        $ingresosPorAno = Factura::where('estado', 'pagado')
            ->selectRaw('YEAR(fecha_emision) as año, SUM(total) as total')
            ->groupBy('año')
            ->orderBy('año', 'desc')
            ->get();

        // Facturas pendientes
        $facturasPendientes = Factura::where('estado', 'pendiente')
            ->sum('total');

        // Facturas pagadas
        $facturasPagadas = Factura::where('estado', 'pagado')
            ->sum('total');

        return view('admin.balance-ingresos', compact(
            'ingresosMesActual',
            'ingresosMesAnterior',
            'ingresosPorAno',
            'facturasPendientes',
            'facturasPagadas'
        ));
    }

    // ─── Reportes detallados ───────────────────────────────────────────────────

    private function periodoDesdeRequest(Request $request): array
    {
        return [
            $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d')),
            $request->get('fecha_fin',    now()->format('Y-m-d')),
        ];
    }

    public function reportePacientes(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->periodoDesdeRequest($request);

        $pacientes = Paciente::with('consultas')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reportes.pacientes', compact('pacientes', 'fechaInicio', 'fechaFin'));
    }

    public function reporteConsultas(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->periodoDesdeRequest($request);

        $consultas = Consulta::with(['paciente', 'medico'])
            ->whereBetween('fecha_consulta', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderBy('fecha_consulta', 'desc')
            ->get();

        return view('admin.reportes.consultas', compact('consultas', 'fechaInicio', 'fechaFin'));
    }

    public function reporteHospitalizaciones(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->periodoDesdeRequest($request);

        $hospitalizaciones = Hospitalizacion::with(['paciente', 'medico'])
            ->whereBetween('fecha_ingreso', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderBy('fecha_ingreso', 'desc')
            ->get();

        return view('admin.reportes.hospitalizaciones', compact('hospitalizaciones', 'fechaInicio', 'fechaFin'));
    }

    public function reporteFinanciero(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->periodoDesdeRequest($request);

        $facturas = Factura::with('paciente')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalIngresos  = $facturas->where('estado', 'pagado')->sum('total');
        $totalPendiente = $facturas->where('estado', 'pendiente')->sum('total');

        return view('admin.reportes.financiero', compact(
            'facturas', 'totalIngresos', 'totalPendiente', 'fechaInicio', 'fechaFin'
        ));
    }

    public function reporteSatisfaccion(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->periodoDesdeRequest($request);

        $encuestas = EncuestaSatisfaccion::with('paciente')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $promedioGeneral = $encuestas->isNotEmpty()
            ? round($encuestas->avg('calidad_general'), 2)
            : 0;

        $recomendarian = $encuestas->where('recomendaria', true)->count();

        return view('admin.reportes.satisfaccion', compact(
            'encuestas', 'promedioGeneral', 'recomendarian', 'fechaInicio', 'fechaFin'
        ));
    }

    public function reporteMedicamentos(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->periodoDesdeRequest($request);

        $tratamientos = Tratamiento::with(['consulta.paciente', 'consulta.medico'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCosto = $tratamientos->sum('costo');

        return view('admin.reportes.medicamentos', compact(
            'tratamientos', 'totalCosto', 'fechaInicio', 'fechaFin'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Reactivar un usuario previamente desactivado.
     *
     * Cambia el estado del usuario a activo, registra la acción para auditoría
     * y redirige al listado con un mensaje informativo.
     *
     * @param int $id Identificador del usuario a reactivar
     * @return \Illuminate\Http\RedirectResponse Redirección al listado tras reactivar al usuario
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el usuario no existe
     */
    public function reactivar($id)
    {
        $usuario = User::findOrFail($id);
        
        $usuario->update(['activo' => 1]);
        
        Log::info('Usuario reactivado', [
            'usuario_id' => $usuario->id,
            'nombre' => $usuario->name,
            'reactivado_por' => Auth::user()->name
        ]);
        
        return redirect()->route('admin.usuarios')
            ->with('success', "Usuario {$usuario->name} reactivado exitosamente");
    }
}