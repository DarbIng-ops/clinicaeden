# 🏥 ClinicaEden v3.0.0

> Sistema de Información Hospitalaria (SIH) — Plataforma white-label de gestión clínica

## 📋 Descripción

ClinicaEden es un Sistema de Información Hospitalaria desarrollado para el Sanatorio Edén (Cajamarca, Colombia). Implementa una arquitectura RBAC con 7 perfiles de usuario que cubren la totalidad del flujo clínico: recepción, consulta médica, hospitalización, enfermería y facturación. Desarrollado como proyecto de grado SENA ADSO Ficha 2977408 — CENIGRAF.

## 🚀 Demo en producción

- **URL:** https://clinica.darbin.tech
- **Repositorio:** https://github.com/DarbIng-ops/clinicaeden

## 🛠️ Stack tecnológico

| Tecnología | Versión |
|---|---|
| Laravel | 12 |
| PHP | 8.2+ |
| MySQL | 8.0+ |
| Livewire | 3.6 |
| Tailwind CSS | 3.4 |
| PHPUnit | 11 |

## 👥 Roles del sistema (RBAC)

1. **admin** — Gestión total: usuarios, estructura, reportes, facturación
2. **recepcionista** — Registro de pacientes, derivaciones, procesamiento de salidas
3. **medico_general** — Consultas, historias clínicas, fórmulas, hospitalizaciones
4. **medico_especialista** — Interconsultas, altas médicas, reportes especializados
5. **jefe_enfermeria** — Supervisión de hospitalizaciones, asignación de auxiliares
6. **auxiliar_enfermeria** — Ejecución de tratamientos, seguimiento de pacientes
7. **cajero** — Procesamiento de pagos, cierre de caja, reportes financieros

## 🔒 Seguridad

- **65/65 tests PHPUnit** pasando (171 assertions activas, 19 skipped por rutas deprecadas)
- **ISO 27001** implementado en rama `feature/iso-seguridad`
- **Audit log** con Observer pattern sobre modelos críticos
- Protecciones activas contra:
  - Escalada de privilegios (RBAC estricto por middleware)
  - SQL Injection (Eloquent ORM + bindings)
  - XSS (escape automático en Blade)
  - CSRF (tokens en todos los formularios)
  - Brute force (Laravel Fortify rate limiting)
  - IDOR (ownership checks en controladores)
  - Mass assignment (listas `$fillable` explícitas)
  - Data exposure (campos sensibles excluidos de `$visible`)
  - Force browsing (todas las rutas protegidas con auth + role)

## 📦 Instalación local (XAMPP)

```bash
# 1. Clonar el repositorio
git clone https://github.com/DarbIng-ops/clinicaeden.git
cd clinicaeden

# 2. Instalar dependencias PHP
composer install

# 3. Copiar variables de entorno
cp .env.example .env

# 4. Configurar la base de datos en .env
# DB_DATABASE=clinicaeden
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Generar clave de aplicación
php artisan key:generate

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Compilar assets frontend
npm install && npm run build

# 8. Iniciar servidor de desarrollo
php artisan serve
```

El sistema queda disponible en `http://localhost:8000`.

## 🌐 Despliegue en producción (Hostinger)

El directorio `public/` del proyecto está mapeado directamente en `~/domains/darbin.tech/public_html/clinica/`, lo que permite servir la aplicación sin un Virtual Host dedicado. Los assets frontend se compilan localmente con `npm run build` antes de cada push, evitando dependencias de Node.js en el servidor. El script `deploy.sh` automatiza el proceso de actualización ejecutando `git pull`, `composer install --no-dev`, `php artisan migrate --force` y `php artisan config:cache` en secuencia.

## 🧪 Ejecutar tests

```bash
php artisan test
```

La suite debe reportar **65/65 tests pasando** (0 fallos).

## 📄 Licencia

Este proyecto opera bajo licencia dual:

- **GPL v3** — libre para uso educativo y no comercial. Ver archivo [LICENSE](./LICENSE).
- **Licencia Comercial** — para implementaciones comerciales o white-label sin obligación de revelar código fuente modificado. Contacto: **alirioportilla96@gmail.com**

© 2026 Alirio Portilla. Todos los derechos reservados sobre la marca ClinicaEden.

## 👨‍💻 Autor

**Alirio Portilla** — [DarbIng-ops](https://github.com/DarbIng-ops)

SENA ADSO — CENIGRAF, Colombia — 2026
