# 🏥 Clínica Edén - Sistema de Gestión Hospitalaria

## 📋 Descripción
Sistema integral de gestión hospitalaria desarrollado con Laravel 12, diseñado para administrar el flujo completo de atención médica desde la recepción hasta la facturación.

## ✨ Características Principales
- Gestión multi-rol (Admin, Recepción, Médicos, Enfermería, Caja)
- Sistema de hospitalizaciones y asignación de habitaciones
- Consultas médicas generales y especializadas
- Gestión de tratamientos y derivaciones
- Facturación y control de pagos
- Encuestas de satisfacción
- Sistema de notificaciones internas
- Reportes y estadísticas

## 🛠️ Stack Tecnológico
- **Backend:** Laravel 12
- **Frontend:** Livewire 3.6, Tailwind CSS 3.4, Alpine.js
- **Autenticación:** Laravel Jetstream con Fortify
- **Base de Datos:** MySQL 8.0+
- **Build Tool:** Vite 7

## 📦 Requisitos del Sistema
- PHP 8.2 o superior
- Composer 2.x
- Node.js 18+ y NPM
- MySQL 8.0+ o MariaDB 10.3+
- XAMPP/WAMP/LAMP (para desarrollo local)

## 🚀 Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/tu-usuario/clinicaeden.git
cd clinicaeden
```

### 2. Instalar Dependencias
```bash
composer install
npm install
```

### 3. Configurar Entorno
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con tus credenciales de base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinicaeden
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Crear Base de Datos
```sql
CREATE DATABASE clinicaeden CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Ejecutar Migraciones
```bash
php artisan migrate
```

### 6. (Opcional) Seeders
```bash
php artisan db:seed
```

### 7. Compilar Assets
```bash
npm run dev
```

### 8. Iniciar Servidor
```bash
php artisan serve
```

Acceder a: http://localhost:8000

## 👥 Roles y Permisos

### Administrador (admin)
- Gestión completa de usuarios
- Asignación de personal a módulos
- Reportes y estadísticas generales
- Balance de ingresos y personal

### Recepcionista (recepcion)
- Registro de pacientes
- Derivación a médicos
- Proceso de salida y encuestas
- Gestión de pagos básica

### Médico General (medico_general)
- Atención de consultas
- Hospitalización de pacientes
- Asignación de tratamientos
- Derivación a especialistas

### Médico Especialista (medico_especialista)
- Consultas especializadas
- Evaluación de pacientes derivados
- Recomendaciones y diagnósticos

### Jefe de Enfermería (jefe_enfermeria)
- Supervisión de auxiliares
- Gestión de tratamientos
- Alta de enfermería
- Asignación de procedimientos

### Auxiliar de Enfermería (auxiliar_enfermeria)
- Ejecución de tratamientos
- Seguimiento de pacientes
- Registro de procedimientos

### Caja (caja)
- Emisión de facturas
- Registro de pagos
- Cierre de caja
- Reportes financieros

## 📁 Estructura del Proyecto

```
clinicaeden/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Controladores por rol
│   │   ├── Middleware/          # RoleMiddleware
│   │   └── Livewire/            # Componentes Livewire
│   ├── Models/                  # Modelos Eloquent
│   └── Notifications/           # Notificaciones del sistema
├── database/
│   ├── migrations/              # Migraciones de BD
│   └── seeders/                 # Datos de prueba
├── resources/
│   ├── views/
│   │   ├── admin/               # Vistas de admin
│   │   ├── recepcion/           # Vistas de recepción
│   │   ├── medico_general/      # Vistas médico general
│   │   ├── medico_especialista/ # Vistas médico especialista
│   │   ├── jefe_enfermeria/     # Vistas jefatura de enfermería
│   │   ├── auxiliar_enfermeria/ # Vistas auxiliares de enfermería
│   │   └── caja/                # Vistas de caja
│   └── components/              # Componentes compartidos
├── routes/
│   ├── web.php                  # Rutas web
│   └── api.php                  # Rutas API
├── tests/
│   ├── Feature/                 # Tests funcionales
│   └── Unit/                    # Tests unitarios
├── public/                      # Assets compilados
├── composer.json                # Dependencias backend
└── package.json                 # Dependencias frontend
```

## 🧪 Testing

### Ejecutar Tests
```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter AdminTest

# Con coverage
php artisan test --coverage
```

### Base de Datos de Testing
```sql
CREATE DATABASE clinicaeden_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 📊 Flujo de Trabajo

1. **Recepción:** Registra paciente → Deriva a médico
2. **Médico General:** Atiende consulta → Hospitaliza (si necesario) → Deriva a especialista (si necesario)
3. **Médico Especialista:** Evalúa paciente derivado → Diagnóstico
4. **Jefe Enfermería:** Asigna auxiliares → Supervisa tratamientos
5. **Auxiliar Enfermería:** Ejecuta tratamientos → Registra procedimientos
6. **Médico/Enfermería:** Da alta médica/enfermería
7. **Caja:** Genera factura → Procesa pago
8. **Recepción:** Encuesta de satisfacción → Salida del paciente

## 🔒 Seguridad

- Autenticación mediante Laravel Jetstream
- Middleware de roles para control de acceso
- Validación de formularios
- Protección CSRF
- Sanitización de inputs
- Passwords hasheados con bcrypt

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/NuevaCaracteristica`)
3. Commit tus cambios (`git commit -m 'Add: nueva característica'`)
4. Push a la rama (`git push origin feature/NuevaCaracteristica`)
5. Abre un Pull Request

## 📝 Convenciones de Código

- PSR-12 para PHP
- Nombres de variables en camelCase
- Nombres de clases en PascalCase
- Nombres de rutas en snake_case
- Vistas en snake_case
- Migraciones descriptivas

## 📄 Licencia

Este proyecto es privado y de uso exclusivo de Clínica Edén.

## 👨‍💻 Autor

Alirio - Desarrollador Principal

## 📞 Soporte

Para consultas o issues: [crear un issue en GitHub]

---

**Última actualización:** Noviembre 2025  
**Versión:** 1.0.0