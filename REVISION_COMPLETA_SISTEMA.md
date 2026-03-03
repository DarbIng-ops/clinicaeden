# REPORTE DE ESTADO — CLÍNICA EDÉN

═══════════════════════════════════════
 REPORTE DE ESTADO — CLÍNICA EDÉN
═══════════════════════════════════════

## 1. RESUMEN EJECUTIVO

- **Porcentaje real de avance estimado:** ~55–60 %. Hay base sólida (modelos, rutas, roles, dashboards por rol) pero varias vistas referenciadas por controladores no existen, una ruta apunta a un método inexistente, y el recurso `ConsultaController` está vacío aunque enlazado a rutas.

- **Qué funciona hoy si corrés `php artisan serve`:**
  - Página pública `/`, login y registro (Jetstream/Fortify).
  - Redirección por rol desde `/dashboard` (admin, recepcionista, medico_general, medico_especialista, jefe_enfermeria, auxiliar_enfermeria, caja).
  - Dashboards por rol que devuelven vistas existentes: admin, recepcion, medico_general, medico_especialista, jefe_enfermeria, auxiliar_enfermeria, caja.
  - Recepción: listado/crear/editar/ver/derivar pacientes, salidas, encuestas (vistas en `recepcion/` y `dashboard/recepcion`).
  - Médico general/especialista: dashboards, pacientes, ver paciente, crear consulta, hospitalizaciones, atender consulta (vistas en `medico_general/`, `medico_especialista/`).
  - Notificaciones: listado y marcar leídas (`notificaciones/index`).
  - Admin: dashboard, usuarios (index, crear, editar, ver), reportes (vistas en `admin/`).
  - Pisos: index, show, create, edit (vistas en `pisos/`).
  - Caja: dashboard, ver factura, procesar pago (vistas en `caja/`).
  - Perfil de usuario y layouts (app, guest).

- **Qué explota o está roto:**
  - **Ruta rota:** `POST /recepcion/pacientes/{paciente}/completar-salida` llama a `RecepcionistaController::completarSalida`, pero el controlador solo tiene `confirmarSalida`. Error de método inexistente al enviar el formulario de completar salida.
  - **Vistas inexistentes** a las que apuntan controladores (generan error 500 o View not found):
    - FacturaController: `facturas.index`, `facturas.create`, `facturas.show`, `facturas.imprimir`, `facturas.reporte` (no existe carpeta `resources/views/facturas/`).
    - EncuestaSatisfaccionController: `encuestas.index`, `encuestas.create`, `encuestas.show`, `encuestas.estadisticas` (no existe carpeta `encuestas/`).
    - HospitalizacionController: `hospitalizaciones.index`, `hospitalizaciones.create`, `hospitalizaciones.show` (no existe carpeta `hospitalizaciones/`).
    - HabitacionController: `habitaciones.index`, `habitaciones.show`, `habitaciones.create`, `habitaciones.edit`, `habitaciones.disponibles` (no existe carpeta `habitaciones/`).
    - ConsultorioController: `consultorios.index`, `consultorios.show`, `consultorios.create`, `consultorios.edit` (no existe carpeta `consultorios/`).
    - ModuloEnfermeriaController: `modulos.show`, `modulos.create`, `modulos.edit` (solo existe `modulos/index.blade.php`).
    - AdminController: `admin.asignaciones.medicos`, `admin.asignaciones.jefes-enfermeria`, `admin.asignaciones.auxiliares`, `admin.balance-personal`, `admin.balance-ingresos` (no existen carpetas `admin/asignaciones/` ni vistas `admin/balance-personal.blade.php`, `admin/balance-ingresos.blade.php`).
    - JefeEnfermeriaController: `jefe-enfermeria.hospitalizaciones.ver`, `jefe-enfermeria.modulos.auxiliares`.
    - AuxiliarEnfermeriaController: `auxiliar-enfermeria.hospitalizaciones.ver`, `auxiliar-enfermeria.procedimientos.index`, `auxiliar-enfermeria.hospitalizaciones.historial-comentarios`, `auxiliar-enfermeria.modulos.index`.
    - CajaController: `caja.reportes.ingresos`, `caja.facturas.buscar`.
  - **ConsultaController:** está registrado como recurso en rutas de medico_general y medico_especialista; todos sus métodos (index, create, store, show, edit, update, destroy) están vacíos (solo comentarios o `//`). No retorna vistas ni datos.
  - **FacturaController:** en `index()` usa `case 'recepcion'` pero el rol en el sistema es `recepcionista`, por lo que recepcionistas nunca entran en ese case y obtendrían colección vacía si se usara esa vista.
  - **Modelo EquipoEnfermeria:** define `jefe_enfermeria_id` y relación con User como jefe; la tabla `equipos_enfermeria` en migración tiene `modulo_id` y `auxiliar_enfermeria_id` (pivot módulo–auxiliar). El modelo no refleja la estructura real de la BD.
  - **Consultorio:** modelo tiene `hasMany(Cita::class)` pero la tabla `citas` no tiene `consultorio_id` en la migración; la relación no es usable.
  - **Paciente:** no define `hospitalizaciones()` ni `facturas()` ni `encuestasSatisfaccion()`; la BD sí tiene FKs. Relaciones faltantes en el modelo.

---

## 2. MODELOS (app/Models/)

| Archivo | Tabla | Relaciones Eloquent | Fillable completo | ¿Falta relación según BD? |
|---------|--------|----------------------|-------------------|----------------------------|
| Paciente.php | pacientes | citas (hasMany), historiaClinica (hasOne), consultas (hasMany) | ✅ | ❌ Faltan: hospitalizaciones, facturas, encuestasSatisfaccion |
| User.php | users | citasComoMedico, citasComoRecepcionista, consultas, hospitalizacionesComoMedicoGeneral, hospitalizacionesComoJefeEnfermeria, hospitalizacionesComoAuxiliar, facturasComoCaja, encuestasSatisfaccionComoRecepcion, notificacionesEnviadas/Recibidas/NoLeidas, modulosComoJefe, modulosComoAuxiliar, equiposEnfermeria | ✅ | ✅ No evidente |
| Cita.php | citas | paciente, medico, recepcionista, canceladaPor (belongsTo); consulta (hasOne) | ✅ | ✅ No evidente (consultorio_id no existe en BD) |
| Consulta.php | consultas | historiaClinica, cita, medico, paciente (belongsTo); tratamientos, formulasMedicas, procedimientos (hasMany); encuestaSatisfaccion (hasOne) | ✅ | ✅ No evidente |
| Hospitalizacion.php | hospitalizaciones | paciente, habitacion, medicoGeneral, jefeEnfermeria, auxiliarEnfermeria (belongsTo); tratamientos, facturas, encuestasSatisfaccion (hasMany) | ✅ | ✅ No evidente |
| Habitacion.php | habitaciones | modulo (belongsTo ModuloEnfermeria), hospitalizaciones, hospitalizacionesActivas (hasMany) | ✅ | ✅ No evidente |
| Factura.php | facturas | paciente, hospitalizacion, consulta, caja (belongsTo) | ✅ | ✅ No evidente |
| HistoriaClinica.php | historias_clinicas | paciente (belongsTo), consultas (hasMany) | ✅ | ✅ No evidente |
| EncuestaSatisfaccion.php | encuestas_satisfaccion | paciente, hospitalizacion, consulta, recepcion (belongsTo User) | ✅ | ✅ No evidente |
| NotificacionSistema.php | notificaciones_sistema | usuarioEmisor, usuarioReceptor (belongsTo User) | ✅ | ✅ No evidente |
| Piso.php | pisos | consultorios, modulosEnfermeria (hasMany); habitaciones (hasManyThrough ModuloEnfermeria) | ✅ | ✅ No evidente |
| ModuloEnfermeria.php | modulos_enfermeria | piso, jefeEnfermeria (belongsTo); auxiliares (belongsToMany equipos_enfermeria), habitaciones, salasProcedimientos (hasMany); hospitalizaciones (hasManyThrough Habitacion) | ✅ | ✅ No evidente |
| Consultorio.php | consultorios | piso (belongsTo), citas (hasMany) | ✅ | ❌ Citas no tiene consultorio_id en BD; relación inválida |
| Tratamiento.php | tratamientos | consulta, hospitalizacion, completadoPor, revisadoPor (belongsTo) | ✅ | ✅ No evidente |
| FormulaMedica.php | formulas_medicas | consulta (belongsTo) | ✅ | ✅ No evidente |
| Procedimiento.php | procedimientos | consulta (belongsTo) | ✅ | ✅ No hay sala_id en procedimientos |
| SalaProcedimiento.php | salas_procedimientos | modulo (belongsTo), procedimientos (hasMany) | ✅ | ❌ Tabla procedimientos no tiene sala_id; relación inválida |
| EquipoEnfermeria.php | equipos_enfermeria | jefeEnfermeria, auxiliarEnfermeria (belongsTo User) | ✅ | ❌ Tabla tiene modulo_id y auxiliar_enfermeria_id; no tiene jefe_enfermeria_id. Modelo desalineado con BD |

---

## 3. CONTROLADORES (app/Http/Controllers/)

| Controlador | Métodos principales | ¿Conectado a rutas? | ¿Lógica real o vacío/stub? |
|-------------|---------------------|----------------------|-----------------------------|
| AdminController | index, usuarios, crearUsuario, guardarUsuario, verUsuario, editarUsuario, actualizarUsuario, eliminarUsuario, reactivar, asignarMedicos, asignarJefesEnfermeria, asignarAuxiliares, reportes, balancePersonal, balanceIngresos | ✅ | ✅ Lógica real; varias vistas que devuelve no existen |
| AuxiliarEnfermeriaController | index, verHospitalizacion, verProcedimientos, completarProcedimiento, agregarComentario, verHistorialComentarios, verModulos | ✅ | ✅ Lógica real; vistas hospitalizaciones.ver, procedimientos.index, historial-comentarios, modulos.index no existen |
| CajaController | index, procesarPago, verFactura, imprimirFactura, mostrarFormularioPago, confirmarPago, reporteIngresos, buscarFacturas, cerrarCaja | ✅ | ✅ Lógica real; vistas reportes.ingresos y facturas.buscar no existen |
| ConsultaController | index, create, store, show, edit, update, destroy | ✅ (recurso en medico_general y medico_especialista) | ❌ Todos vacíos (stub) |
| ConsultorioController | index, show, create, store, edit, update, destroy | ✅ | ✅ Lógica real; vistas consultorios.* no existen |
| DashboardController | index, dashboardAdmin, dashboardRecepcion, etc. | ✅ | ✅ Redirige por rol; vistas dashboard.* existen |
| EncuestaSatisfaccionController | index, create, store, show, estadisticas, buscarPaciente | ✅ | ✅ Lógica real; vistas encuestas.* no existen |
| FacturaController | index, create, store, show, imprimir, buscarPaciente, calcularCosto, reporteIngresos | ✅ | ✅ Lógica real; case 'recepcion' debería ser 'recepcionista'; vistas facturas.* no existen |
| HabitacionController | index, show, create, store, edit, update, destroy, disponibles | ✅ | ✅ Lógica real; vistas habitaciones.* no existen |
| HospitalizacionController | index, create, store, show, asignarAuxiliar, darAltaMedica, darAltaEnfermeria, completarAlta, habitacionesDisponibles | ✅ | ✅ Lógica real; vistas hospitalizaciones.* no existen |
| JefeEnfermeriaController | index, verHospitalizacion, revisarTratamiento, asignarAuxiliar, darAltaEnfermeria, verAuxiliares | ✅ | ✅ Lógica real; vistas hospitalizaciones.ver y modulos.auxiliares no existen |
| MedicoEspecialistaController | index, pacientes, verPaciente, crearConsulta, storeConsulta, estadisticas, hospitalizaciones | ✅ | ✅ Lógica real |
| MedicoGeneralController | index, pacientes, verPaciente, crearConsulta, storeConsulta, hospitalizaciones, crearHospitalizacion, storeHospitalizacion, atenderConsulta, finalizarConsulta | ✅ | ✅ Lógica real |
| ModuloEnfermeriaController | index, show, create, store, edit, update, destroy, asignarAuxiliar, desasignarAuxiliar | ✅ | ✅ Lógica real; vistas modulos.show, create, edit no existen |
| NotificacionController | index, marcarLeida, marcarTodasLeidas | ✅ | ✅ Lógica real |
| PisoController | index, show, create, store, edit, update, destroy | ✅ | ✅ Lógica real |
| RecepcionistaController | dashboard, index, crear, store, show, edit, update, destroy, buscarPorDni, derivarPaciente, crearConsulta, salidas, procesarSalida, confirmarSalida | ✅ | ✅ Lógica real; ruta completar-salida apunta a completarSalida que no existe |

---

## 4. COMPONENTES LIVEWIRE (app/Livewire/)

| Componente | Módulo | Funcionalidades | ¿Vista en resources/views/livewire/? |
|------------|--------|-----------------|---------------------------------------|
| Pacientes\ListarPacientes | Pacientes | Búsqueda, paginación, listado activos | ✅ listar-pacientes.blade.php |
| Pacientes\VerPaciente | Pacientes | Ver detalle con historiaClinica, citas, consultas | ✅ ver-paciente.blade.php |
| Pacientes\CrearPaciente | Pacientes | Formulario crear + HistoriaClinica, validación | ✅ crear-paciente.blade.php |
| Pacientes\EditarPaciente | Pacientes | Editar paciente, foto | ✅ editar-paciente.blade.php |

---

## 5. VISTAS (resources/views/)

- **Existen para cada controlador:** Solo parcialmente. Recepción, medico_general, medico_especialista, admin (dashboard, usuarios, reportes), pisos, caja (dashboard, facturas/ver, facturas/procesar-pago), notificaciones, livewire/pacientes.
- **Layouts:** app.blade.php, guest.blade.php, adminlte.blade.php.
- **Vistas huérfanas o sin uso directo:** recepcionista/dashboard.blade.php; terms.blade.php, policy.blade.php (estáticas).
- **Carpetas/vistas faltantes:** facturas/, encuestas/, hospitalizaciones/, habitaciones/, consultorios/; admin/asignaciones/, admin/balance-personal.blade.php, admin/balance-ingresos.blade.php; jefe-enfermeria/hospitalizaciones/, jefe-enfermeria/modulos/; auxiliar-enfermeria/hospitalizaciones/, auxiliar-enfermeria/procedimientos/, auxiliar-enfermeria/modulos/; caja/reportes/, caja/facturas/buscar; modulos/show, create, edit; pisos/create, edit (verificar si existen).

---

## 6. RUTAS (routes/web.php y api.php)

- **Total:** Muchas rutas web agrupadas por auth y role; api solo GET /user (auth:sanctum).
- **Middleware de roles:** Sí; RoleMiddleware con roles admin, recepcionista, medico_general, medico_especialista, jefe_enfermeria, auxiliar_enfermeria, caja.
- **Rutas rotas o vacías:** POST completar-salida → método completarSalida inexistente. Recurso consultas → ConsultaController con métodos vacíos.
- **Rutas faltantes por módulo:** No; lo que falta son vistas para rutas ya definidas.

---

## 7. BASE DE DATOS (database/migrations/ y seeders/)

| Tabla / concepto | ¿Migración? | ¿Seeder? | ¿Model? |
|------------------|-------------|---------|--------|
| pacientes | ✅ | ❌ | ✅ |
| users (medico/enfermería) | ✅ | ✅ UsersTableSeeder, HabitacionesSeeder (jefes/auxiliares), CajaUserSeeder | ✅ |
| especialidad | N/A (campo en users) | N/A | N/A |
| citas | ✅ | ❌ | ✅ |
| historias_clinicas | ✅ | ❌ | ✅ |
| diagnostico | N/A (campo en consultas) | N/A | N/A |
| hospitalizaciones | ✅ | ❌ | ✅ |
| habitaciones | ✅ | ✅ HabitacionesSeeder | ✅ |
| tipo_habitacion | N/A (enum en habitaciones) | N/A | N/A |
| facturas | ✅ | ❌ | ✅ |
| detalle_fact | ❌ | ❌ | ❌ |

Otras tablas: consultas, tratamientos, formulas_medicas, procedimientos, encuestas_satisfaccion, notificaciones_sistema, notifications, pisos, modulos_enfermeria, equipos_enfermeria, salas_procedimientos, consultorios. Seeders: DatabaseSeeder llama UsersTableSeeder, HabitacionesSeeder, CajaUserSeeder.

---

## 8. AUTENTICACIÓN Y ROLES

- **RBAC:** Middleware RoleMiddleware (alias `role`). Sin Gates ni Policies para roles.
- **7 roles:** admin, recepcionista, medico_general, medico_especialista, jefe_enfermeria, auxiliar_enfermeria, caja. Cada uno tiene dashboard/panel propio en rutas y vista de dashboard existente.

---

## 9. MÓDULOS POR ESTADO

| Módulo            | Modelo | Migración | Controlador | Livewire | Vistas | Rutas | Estado    |
|-------------------|--------|-----------|-------------|----------|--------|-------|-----------|
| Pacientes         | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | 🔶 Parcial (relaciones modelo; ruta completarSalida) |
| Consultas/Citas   | ✅ | ✅ | 🔶 | ❌ | 🔶 | ✅ | 🔶 Parcial |
| Historial Médico  | ✅ | ✅ | N/A | ❌ | 🔶 | ✅ | 🔶 Parcial |
| Hospitalización   | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | 🔶 Parcial |
| Habitaciones      | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | 🔶 Parcial |
| Facturación       | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | 🔶 Parcial |
| Personal Médico   | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ Completo |
| Especialidades    | ❌ | N/A | N/A | ❌ | N/A | N/A | ❌ Sin implementar |
| Reportes          | N/A | N/A | ✅ | ❌ | 🔶 | ✅ | 🔶 Parcial |
| Notificaciones    | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ Completo |

Estados: ✅ Completo | 🔶 Parcial | ❌ Sin implementar

---

## 10. DEUDA TÉCNICA

- **TODO en vistas:** medico_general (evolución clínica, detalle consulta, tipo consulta, búsqueda pacientes, adjuntos); medico_especialista (detalle consulta, laboratorios, acciones, agenda, métricas por especialidad, mostrar especialidad).
- **Métodos vacíos o null:** ConsultaController todos los métodos; User getEdadAttribute/getDiplomaUrlAttribute retornan null cuando corresponde.
- **N+1:** No auditado exhaustivamente; en varios listados hay with().
- **Validaciones:** confirmarSalida usa nombres de campo distintos al modelo EncuestaSatisfaccion en parte.
- **Inconsistencias:** FacturaController 'recepcion' vs rol 'recepcionista'; EquipoEnfermeria vs tabla; Consultorio->citas y SalaProcedimiento->procedimientos sin FK; ruta completar-salida.

---

## 11. PRÓXIMOS 3 PASOS (ordenados por prioridad)

**Paso 1:** Corregir la ruta de salida: que `pacientes.completar-salida` apunte a `confirmarSalida` o implementar `completarSalida` que use la lógica actual de confirmación. Así el flujo de completar salida no rompe.

**Paso 2:** Crear las vistas faltantes que provocan View not found: facturas/, encuestas/, hospitalizaciones/, habitaciones/, consultorios/; admin asignaciones y balance-personal, balance-ingresos; jefe-enfermeria (hospitalizaciones/ver, modulos/auxiliares); auxiliar-enfermeria (hospitalizaciones/ver, procedimientos/index, historial-comentarios, modulos/index); caja (facturas/buscar, reportes/ingresos); modulos (show, create, edit) según uso real.

**Paso 3:** Alinear modelos con la BD y corregir bugs: (1) Paciente: añadir hospitalizaciones(), facturas(), encuestasSatisfaccion(). (2) EquipoEnfermeria: reflejar tabla (modulo_id, auxiliar_enfermeria_id). (3) Revisar Consultorio->citas y SalaProcedimiento->procedimientos (FK o quitar relación). (4) FacturaController::index() usar 'recepcionista'. (5) Implementar o quitar resource ConsultaController de rutas.

═══════════════════════════════════════

*Reporte basado únicamente en lectura del código. Solo se documenta lo que existe en el proyecto.*
