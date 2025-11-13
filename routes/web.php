<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\MedicoGeneralController;
use App\Http\Controllers\MedicoEspecialistaController;
use App\Http\Controllers\RecepcionistaController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HospitalizacionController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\EncuestaSatisfaccionController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\ModuloEnfermeriaController;
use App\Http\Controllers\ConsultorioController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\JefeEnfermeriaController;
use App\Http\Controllers\AuxiliarEnfermeriaController;
// ============================================================
// Página principal pública
// ============================================================
Route::get('/', function () {
    return view('welcome');
});

// ============================================================
// Dashboard genérico: determina rol y redirige al panel correcto
// ============================================================
Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ============================================================
// Notificaciones: accesibles para cualquier usuario autenticado
// ============================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/{id}/marcar-leida', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcar-leida');
    Route::post('/notificaciones/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.marcar-todas-leidas');
});

// ============================================================
// Administrador: gestión integral del sistema
// ============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Gestión de estructura (pisos, módulos, consultorios, habitaciones)
    Route::get('/asignaciones/medicos', [AdminController::class, 'asignarMedicos'])->name('asignaciones.medicos');
    Route::get('/asignaciones/jefes-enfermeria', [AdminController::class, 'asignarJefesEnfermeria'])->name('asignaciones.jefes-enfermeria');
    Route::get('/asignaciones/auxiliares', [AdminController::class, 'asignarAuxiliares'])->name('asignaciones.auxiliares');

    // Gestión de usuarios
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
    Route::get('/usuarios/crear', [AdminController::class, 'crearUsuario'])->name('usuarios.crear');
    Route::post('/usuarios', [AdminController::class, 'guardarUsuario'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}', [AdminController::class, 'verUsuario'])->name('usuarios.ver');
    Route::get('/usuarios/{usuario}/editar', [AdminController::class, 'editarUsuario'])->name('usuarios.editar');
    Route::put('/usuarios/{usuario}', [AdminController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::delete('/usuarios/{usuario}', [AdminController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
    Route::patch('/usuarios/{id}/reactivar', [AdminController::class, 'reactivar'])->name('usuarios.reactivar');

    // Gestión de pacientes y hospitalización
    // (No hay rutas específicas del administrador en esta sección por el momento)

    // Reportes y configuración
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/reportes', [AdminController::class, 'reportes'])->name('reportes');
    Route::get('/balance-personal', [AdminController::class, 'balancePersonal'])->name('balance-personal');
    Route::get('/balance-ingresos', [AdminController::class, 'balanceIngresos'])->name('balance-ingresos');
});

// ============================================================
// Recepción: gestión de pacientes, derivaciones y encuestas
// ============================================================

Route::middleware(['auth', 'role:recepcionista'])->prefix('recepcion')->name('recepcion.')->group(function () {
    // Dashboard de recepción
    Route::get('/dashboard', [RecepcionistaController::class, 'dashboard'])->name('dashboard');
    
    // Rutas CRUD completas de pacientes
    Route::get('/pacientes', [RecepcionistaController::class, 'index'])->name('pacientes.index');
    Route::get('/pacientes/crear', [RecepcionistaController::class, 'crear'])->name('pacientes.crear');
    Route::post('/pacientes', [RecepcionistaController::class, 'store'])->name('pacientes.store');
    Route::get('/pacientes/{paciente}', [RecepcionistaController::class, 'show'])->name('pacientes.show');
    Route::get('/pacientes/{paciente}/editar', [RecepcionistaController::class, 'edit'])->name('pacientes.edit');
    Route::put('/pacientes/{paciente}', [RecepcionistaController::class, 'update'])->name('pacientes.update');
    Route::get('/pacientes/{paciente}/derivar', [RecepcionistaController::class, 'derivarPaciente'])->name('pacientes.derivar');
    Route::post('/consultas', [RecepcionistaController::class, 'crearConsulta'])->name('consultas.store');
    Route::delete('/pacientes/{paciente}', [RecepcionistaController::class, 'destroy'])->name('pacientes.destroy');
    
    // Gestión de salida de pacientes
    Route::get('/salidas', [RecepcionistaController::class, 'salidas'])->name('salidas');
    Route::get('/pacientes/{paciente}/salida', [RecepcionistaController::class, 'procesarSalida'])->name('pacientes.salida');
    Route::get('/pacientes/{paciente}/procesar-salida', [RecepcionistaController::class, 'procesarSalida'])->name('procesar-salida');
    Route::post('/pacientes/{paciente}/completar-salida', [RecepcionistaController::class, 'completarSalida'])->name('pacientes.completar-salida');
    Route::post('/pacientes/{paciente}/confirmar-salida', [RecepcionistaController::class, 'confirmarSalida'])->name('confirmar-salida');
    
    // Encuestas de satisfacción
    Route::get('/encuestas', [RecepcionistaController::class, 'encuestas'])->name('encuestas.index');
    Route::get('/encuestas/crear/{paciente}', [RecepcionistaController::class, 'crearEncuesta'])->name('encuestas.crear');
    Route::post('/encuestas', [RecepcionistaController::class, 'guardarEncuesta'])->name('encuestas.guardar');
    
    // Rutas AJAX para búsquedas
    Route::get('/api/buscar-paciente-dni', [RecepcionistaController::class, 'buscarPorDni'])->name('api.buscar-paciente-dni');
});

// ============================================================
// Estructura hospitalaria: pisos, módulos, consultorios, habitaciones
// ============================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Pisos
    Route::resource('pisos', PisoController::class);
    Route::get('pisos/{piso}/modulos', [PisoController::class, 'show'])->name('pisos.modulos');

    // Módulos de Enfermería
    Route::resource('modulos', ModuloEnfermeriaController::class);
    Route::post('modulos/{modulo}/asignar-auxiliar', [ModuloEnfermeriaController::class, 'asignarAuxiliar'])->name('modulos.asignar-auxiliar');
    Route::delete('modulos/{modulo}/desasignar-auxiliar/{auxiliar}', [ModuloEnfermeriaController::class, 'desasignarAuxiliar'])->name('modulos.desasignar-auxiliar');

    // Consultorios
    Route::resource('consultorios', ConsultorioController::class);

    // Habitaciones
    Route::resource('habitaciones', HabitacionController::class);
    Route::get('habitaciones-disponibles', [HabitacionController::class, 'disponibles'])->name('habitaciones.disponibles');
});

// ============================================================
// Hospitalización: admisión, altas y disponibilidad
// ============================================================
Route::middleware(['auth', 'role:admin,medico_general,medico_especialista'])->group(function () {
    Route::resource('hospitalizaciones', HospitalizacionController::class);
    Route::post('hospitalizaciones/{hospitalizacion}/asignar-auxiliar', [HospitalizacionController::class, 'asignarAuxiliar'])->name('hospitalizaciones.asignar-auxiliar');
    Route::post('hospitalizaciones/{hospitalizacion}/alta-medica', [HospitalizacionController::class, 'darAltaMedica'])->name('hospitalizaciones.alta-medica');
    Route::post('hospitalizaciones/{hospitalizacion}/alta-enfermeria', [HospitalizacionController::class, 'darAltaEnfermeria'])->name('hospitalizaciones.alta-enfermeria');
    Route::post('hospitalizaciones/{hospitalizacion}/completar-alta', [HospitalizacionController::class, 'completarAlta'])->name('hospitalizaciones.completar-alta');
    Route::get('api/habitaciones-disponibles', [HospitalizacionController::class, 'habitacionesDisponibles'])->name('api.habitaciones-disponibles');
});

// ============================================================
// Facturación: emisión de facturas y cálculos auxiliares
// ============================================================
Route::middleware(['auth', 'role:admin,caja'])->group(function () {
    Route::resource('facturas', FacturaController::class);
    Route::get('facturas/{factura}/imprimir', [FacturaController::class, 'imprimir'])->name('facturas.imprimir');
    Route::get('api/buscar-paciente', [FacturaController::class, 'buscarPaciente'])->name('api.buscar-paciente');
    Route::get('api/calcular-costo', [FacturaController::class, 'calcularCosto'])->name('api.calcular-costo');
    Route::get('facturas/reporte/ingresos', [FacturaController::class, 'reporteIngresos'])->name('facturas.reporte-ingresos');
});

// ============================================================
// Encuestas de satisfacción: registro y estadísticas
// ============================================================
Route::middleware(['auth', 'role:admin,recepcionista'])->group(function () {
    Route::resource('encuestas', EncuestaSatisfaccionController::class);
    Route::get('encuestas/estadisticas', [EncuestaSatisfaccionController::class, 'estadisticas'])->name('encuestas.estadisticas');
    Route::get('api/encuestas/buscar-paciente', [EncuestaSatisfaccionController::class, 'buscarPaciente'])->name('api.encuestas.buscar-paciente');
});

// ============================================================
// Rutas específicas por rol - Dashboards especializados
// ============================================================
Route::middleware(['auth', 'role:medico_general'])->prefix('medico-general')->name('medico_general.')->group(function () {
    Route::get('/dashboard', [MedicoGeneralController::class, 'index'])->name('dashboard');
    
    // Rutas de Consultas
    Route::resource('consultas', ConsultaController::class);
    Route::get('/consultas/{consulta}/atender', [MedicoGeneralController::class, 'atenderConsulta'])->name('consultas.atender');
    Route::post('/consultas/{consulta}/finalizar', [MedicoGeneralController::class, 'finalizarConsulta'])->name('consultas.finalizar');
    
    // Rutas de Pacientes
    Route::get('/pacientes', [MedicoGeneralController::class, 'pacientes'])->name('pacientes.index');
    Route::get('/pacientes/{paciente}', [MedicoGeneralController::class, 'verPaciente'])->name('pacientes.show');
    Route::get('/pacientes/{paciente}/crear-consulta', [MedicoGeneralController::class, 'crearConsulta'])->name('pacientes.crear-consulta');
    Route::post('/pacientes/{paciente}/consultas', [MedicoGeneralController::class, 'storeConsulta'])->name('pacientes.store-consulta');
    
    // Rutas de Hospitalizaciones
    Route::get('/hospitalizaciones', [MedicoGeneralController::class, 'hospitalizaciones'])->name('hospitalizaciones.index');
    Route::get('/hospitalizaciones/crear', [MedicoGeneralController::class, 'crearHospitalizacion'])->name('hospitalizaciones.crear');
    Route::post('/hospitalizaciones', [MedicoGeneralController::class, 'storeHospitalizacion'])->name('hospitalizaciones.store');
    
    // Rutas de Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/{id}/marcar-leida', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcar-leida');
});
Route::middleware(['auth', 'role:medico_especialista'])->prefix('medico-especialista')->name('medico_especialista.')->group(function () {
    Route::get('/dashboard', [MedicoEspecialistaController::class, 'index'])->name('dashboard');
    
    // Rutas de Consultas Especializadas
    Route::resource('consultas', ConsultaController::class);
    
    // Rutas de Pacientes
    Route::get('/pacientes', [MedicoEspecialistaController::class, 'pacientes'])->name('pacientes.index');
    Route::get('/pacientes/{paciente}', [MedicoEspecialistaController::class, 'verPaciente'])->name('pacientes.show');
    Route::get('/pacientes/{paciente}/crear-consulta', [MedicoEspecialistaController::class, 'crearConsulta'])->name('pacientes.crear-consulta');
    Route::post('/pacientes/{paciente}/consultas', [MedicoEspecialistaController::class, 'storeConsulta'])->name('pacientes.store-consulta');
    
    // Rutas de Hospitalizaciones
    Route::get('/hospitalizaciones', [MedicoEspecialistaController::class, 'hospitalizaciones'])->name('hospitalizaciones.index');
    
    // Rutas de Estadísticas
    Route::get('/estadisticas', [MedicoEspecialistaController::class, 'estadisticas'])->name('estadisticas');
    
    // Rutas de Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/{id}/marcar-leida', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcar-leida');
});
Route::middleware(['auth', 'role:jefe_enfermeria'])->prefix('jefe-enfermeria')->name('jefe-enfermeria.')->group(function () {
    Route::get('/dashboard', [JefeEnfermeriaController::class, 'index'])->name('dashboard');
    
    // Gestión de hospitalizaciones
    Route::get('/hospitalizaciones/{hospitalizacion}', [JefeEnfermeriaController::class, 'verHospitalizacion'])->name('hospitalizaciones.ver');
    Route::post('/hospitalizaciones/{hospitalizacion}/asignar-auxiliar', [JefeEnfermeriaController::class, 'asignarAuxiliar'])->name('hospitalizaciones.asignar-auxiliar');
    Route::post('/hospitalizaciones/{hospitalizacion}/alta-enfermeria', [JefeEnfermeriaController::class, 'darAltaEnfermeria'])->name('hospitalizaciones.alta-enfermeria');
    
    // Gestión de tratamientos
    Route::post('/tratamientos/{tratamiento}/revisar', [JefeEnfermeriaController::class, 'revisarTratamiento'])->name('tratamientos.revisar');
    
    // Gestión de módulos y auxiliares
    Route::get('/modulos/{modulo}/auxiliares', [JefeEnfermeriaController::class, 'verAuxiliares'])->name('modulos.auxiliares');
});
Route::middleware(['auth', 'role:auxiliar_enfermeria'])->prefix('auxiliar-enfermeria')->name('auxiliar-enfermeria.')->group(function () {
    Route::get('/dashboard', [AuxiliarEnfermeriaController::class, 'index'])->name('dashboard');
    
    // Gestión de hospitalizaciones asignadas
    Route::get('/hospitalizaciones/{hospitalizacion}', [AuxiliarEnfermeriaController::class, 'verHospitalizacion'])->name('hospitalizaciones.ver');
    Route::post('/hospitalizaciones/{hospitalizacion}/comentario', [AuxiliarEnfermeriaController::class, 'agregarComentario'])->name('hospitalizaciones.comentario');
    Route::get('/hospitalizaciones/{hospitalizacion}/historial-comentarios', [AuxiliarEnfermeriaController::class, 'verHistorialComentarios'])->name('hospitalizaciones.historial-comentarios');
    
    // Gestión de procedimientos
    Route::get('/procedimientos', [AuxiliarEnfermeriaController::class, 'verProcedimientos'])->name('procedimientos.index');
    Route::post('/procedimientos/{tratamiento}/completar', [AuxiliarEnfermeriaController::class, 'completarProcedimiento'])->name('procedimientos.completar');
    
    // Gestión de módulos
    Route::get('/modulos', [AuxiliarEnfermeriaController::class, 'verModulos'])->name('modulos.index');
});
Route::middleware(['auth', 'role:caja'])->prefix('caja')->name('caja.')->group(function () {
    Route::get('/dashboard', [CajaController::class, 'index'])->name('dashboard');
    
    // Gestión de facturas y pagos
    Route::get('/facturas', [CajaController::class, 'buscarFacturas'])->name('facturas.buscar');
    Route::get('/facturas/{factura}', [CajaController::class, 'verFactura'])->name('facturas.ver');
    Route::get('/facturas/{factura}/procesar-pago', [CajaController::class, 'mostrarFormularioPago'])->name('facturas.procesar-pago');
    Route::post('/facturas/{factura}/confirmar-pago', [CajaController::class, 'confirmarPago'])->name('facturas.confirmar-pago');
    Route::get('/facturas/{factura}/imprimir', [CajaController::class, 'imprimirFactura'])->name('facturas.imprimir');
    
    // Reportes
    Route::get('/reportes/ingresos', [CajaController::class, 'reporteIngresos'])->name('reportes.ingresos');
    
    // Cierre de caja
    Route::post('/cerrar-caja', [CajaController::class, 'cerrarCaja'])->name('cerrar-caja');
});