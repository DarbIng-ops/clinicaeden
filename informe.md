# Auditoría Vistas Médicas

## Auditoría Vistas Médicas

### Mapeo Completo
| Carpeta | Archivos `.blade.php` | Observaciones |
| --- | --- | --- |
| `medico` | `dashboard.blade.php` | Único archivo; no existe ruta `medico.dashboard`; parece panel legacy basado en AdminLTE. |
| `medico_general` | `dashboard.blade.php`, `hospitalizaciones.blade.php`, `pacientes.blade.php`, `consultas/atender.blade.php` | El controlador espera además `ver-paciente`, `crear-consulta`, `crear-hospitalizacion`; faltan físicamente. |
| `medico-general` | `dashboard.blade.php` | Duplicado del dashboard “nuevo” (Tailwind) pero con rutas hyphen-case; sin referencias desde controladores. |
| `medico_especialista` | `dashboard.blade.php`, `hospitalizaciones.blade.php`, `pacientes.blade.php` | Controlador invoca vistas inexistentes `ver-paciente`, `crear-consulta`, `estadisticas`. |
| `medico-especialista` | `dashboard.blade.php` | Copia del dashboard especialista con rutas hyphen-case. |

**Carpetas duplicadas/legacy**
- `medico` y las variantes hyphen-case (`medico-general`, `medico-especialista`) no tienen referencias activas y duplican funcionalidades de las carpetas snake_case; candidatas a eliminación tras confirmar con negocio.
- Carpetas snake_case (`medico_general`, `medico_especialista`) son las únicas enlazadas desde controladores, pero están incompletas (faltan vistas usadas).

### Propuesta de Estandarización
| Carpeta actual | Carpeta nueva (kebab-case) | Acción |
| --- | --- | --- |
| `medico_general` | `medico-general` | Renombrar carpeta; completar vistas faltantes antes o después del rename. |
| `medico_especialista` | `medico-especialista` | Renombrar carpeta; crear las vistas faltantes. |
| `medico-general` | — | Eliminar tras migrar contenido/confirmar no uso. |
| `medico-especialista` | — | Eliminar tras migrar contenido/confirmar no uso. |
| `medico` | — | Eliminar si se confirma que el flujo legacy queda obsoleto; si se requiere, migrar a `medico-general` como variante. |

### Impacto en Controladores
- `MedicoGeneralController` referencia vistas snake_case en múltiples métodos, todas impactadas por el rename propuesto:  
```44:49:app/Http/Controllers/MedicoGeneralController.php
return view('medico_general.dashboard', compact(
    'pacientesHospitalizados',
    'consultasHoy',
    'consultasPendientes',
    'notificacionesNoLeidas'
));
```
```65:66:app/Http/Controllers/MedicoGeneralController.php
return view('medico_general.pacientes', compact('pacientes'));
```
```89:93:app/Http/Controllers/MedicoGeneralController.php
return view('medico_general.ver-paciente', compact('paciente', 'hospitalizacion'));
```
```193:194:app/Http/Controllers/MedicoGeneralController.php
return view('medico_general.crear-hospitalizacion', compact('pacientes', 'habitaciones'));
```
```211:212:app/Http/Controllers/MedicoGeneralController.php
return view('medico_general.consultas.atender', compact('consulta'));
```
- `MedicoEspecialistaController` igualmente depende de la nomenclatura snake_case:  
```43:48:app/Http/Controllers/MedicoEspecialistaController.php
return view('medico_especialista.dashboard', compact(
    'consultasEspecializadas',
    'pacientesEspecialidad',
    'tratamientosEspecializados',
    'notificacionesNoLeidas'
));
```
```66:67:app/Http/Controllers/MedicoEspecialistaController.php
return view('medico_especialista.pacientes', compact('pacientes'));
```
```92:93:app/Http/Controllers/MedicoEspecialistaController.php
return view('medico_especialista.ver-paciente', compact('paciente'));
```
```113:114:app/Http/Controllers/MedicoEspecialistaController.php
return view('medico_especialista.crear-consulta', compact('paciente'));
```
```202:203:app/Http/Controllers/MedicoEspecialistaController.php
return view('medico_especialista.estadisticas', compact('estadisticas'));
```
```224:224:app/Http/Controllers/MedicoEspecialistaController.php
return view('medico_especialista.hospitalizaciones', compact('hospitalizaciones'));
```
- `DashboardRedirectController` intenta redirigir a una ruta inexistente `medico.dashboard`, indicador de deuda legacy alineada con la carpeta `medico/`:  
```16:19:app/Http/Controllers/DashboardRedirectController.php
return match($user->role) {
    'admin' => redirect()->route('admin.dashboard'),
    'medico' => redirect()->route('medico.dashboard'),
```

### Plan de Migración
- **Script propuesto (no ejecutar aún):**
```bash
#!/usr/bin/env bash
set -euo pipefail

cd resources/views

# Respaldar carpetas legacy por si acaso
tar -czf medico-legacy-$(date +%Y%m%d).tar.gz medico medico-general medico-especialista

# Renombrar a kebab-case
mv medico_general medico-general
mv medico_especialista medico-especialista

# Opcional: retirar carpetas legacy tras verificación
rm -rf medico medico-general-legacy medico-especialista-legacy
```
(Ajustar comandos `mv`/`rm` a `ren`/`rmdir` si se usa PowerShell, o ejecutar desde Git Bash.)

- **Archivos a actualizar tras el rename:**
  - `app/Http/Controllers/MedicoGeneralController.php`
  - `app/Http/Controllers/MedicoEspecialistaController.php`
  - `app/Http/Controllers/DashboardRedirectController.php` (revisar ruta legacy y decidir si apunta a `medico_general.dashboard` o se elimina el caso).
  - Rutas o tests que mencionen `medico_general.` y `medico_especialista.` en strings (buscar `medico_general.` y `medico_especialista.`).

- **Orden sugerido:**
  1. Crear respaldo de las carpetas y auditar con producto qué vistas legacy se conservan.
  2. Completar/crear las vistas faltantes (`ver-paciente`, `crear-consulta`, etc.) dentro de las carpetas definitivas antes del rename o inmediatamente después.
  3. Renombrar carpetas a kebab-case y actualizar todos los `return view()` correspondientes.
  4. Ajustar rutas/controller para eliminar referencias a `medico.dashboard` y confirmar nomenclatura consistente (prefijos, namespacing de rutas, helpers `route()`).
  5. Ejecutar pruebas manuales/automáticas (login con roles `medico_general` y `medico_especialista`) para validar que las vistas cargan.
  6. Eliminar carpetas legacy (`medico`, `medico-general`, `medico-especialista`) una vez verificado que no quedan referencias.

No se ejecutó ningún comando; análisis listo para referencia y planificación.