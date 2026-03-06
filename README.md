<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Livewire-3.6-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3.6">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0">
  <img src="https://img.shields.io/badge/TailwindCSS-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
</p>

# 🏥 Clínica Edén - Sistema de Gestión Hospitalaria

Sistema integral de gestión hospitalaria desarrollado con Laravel 12, diseñado para optimizar procesos administrativos, médicos y de enfermería en instituciones de salud.

## 📋 Tabla de Contenidos

- [Características Principales](#-características-principales)
- [Stack Tecnológico](#-stack-tecnológico)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Roles y Permisos](#-roles-y-permisos)
- [Módulos del Sistema](#-módulos-del-sistema)
- [Pruebas](#-pruebas)
- [Credenciales de Demo](#-credenciales-de-demo)
- [Documentación Adicional](#-documentación-adicional)
- [Roadmap](#-roadmap)
- [Contribución](#-contribución)
- [Licencia](#-licencia)
- [Contacto](#-contacto)

---

## ✨ Características Principales

- 🔐 **Autenticación Robusta**: Sistema de login con Laravel Fortify + Jetstream, 2FA opcional
- 👥 **Sistema de Roles**: 7 roles distintos con permisos granulares (RBAC)
- 📊 **Dashboards Personalizados**: Interfaces específicas para cada rol de usuario
- 🏥 **Gestión Hospitalaria Completa**: Pisos, módulos, habitaciones y consultorios
- 👨‍⚕️ **Gestión Médica**: Consultas, historias clínicas, fórmulas médicas, tratamientos
- 🛏️ **Hospitalización**: Admisiones, asignación de personal, seguimiento de pacientes
- 💰 **Facturación Integrada**: Generación automática de facturas, procesamiento de pagos
- 🔔 **Notificaciones en Tiempo Real**: Sistema de alertas para eventos críticos
- 📈 **Reportes y Estadísticas**: Análisis de datos médicos y financieros
- 📱 **Responsive Design**: Interfaz optimizada para desktop, tablet y móvil

---

## 🛠️ Stack Tecnológico

### Backend
- **Framework**: Laravel 12.0
- **Lenguaje**: PHP 8.2+
- **Autenticación**: Laravel Fortify + Jetstream
- **API Authentication**: Laravel Sanctum 4.0
- **ORM**: Eloquent

### Frontend
- **Framework CSS**: Tailwind CSS 3.4
- **Componentes Interactivos**: Livewire 3.6.4
- **Build Tool**: Vite 7.0.7
- **Icons**: Heroicons (via Blade)

### Base de Datos
- **Motor**: MySQL 8.0 / MariaDB
- **Migraciones**: 35 archivos de migración
- **Seeders**: Datos de prueba incluidos

### Testing
- **Framework**: PHPUnit 11.5
- **Cobertura**: 28 pruebas automatizadas (Feature + Unit)

---

## 📦 Requisitos del Sistema

### Requisitos Mínimos

- PHP >= 8.2
- Composer 2.x
- Node.js >= 18.x
- NPM >= 9.x
- MySQL >= 8.0 o MariaDB >= 10.6
- Extensiones PHP requeridas:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - cURL

### Entorno de Desarrollo Recomendado

- **Windows**: XAMPP 8.2+ con MySQL
- **macOS/Linux**: Laravel Valet o Docker (Laravel Sail)
- **Editor**: VS Code, PhpStorm, Cursor IDE

---

## 🚀 Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/DarbIng-ops/clinicaeden.git
cd clinicaeden
```

### 2. Instalar Dependencias
```bash
# Dependencias PHP
composer install

# Dependencias Node.js
npm install
```

### 3. Configurar Variables de Entorno
```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar Base de Datos

Editar el archivo `.env` con tus credenciales:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinicaeden
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 5. Ejecutar Migraciones y Seeders
```bash
# Crear tablas en la base de datos
php artisan migrate

# Poblar con datos de ejemplo
php artisan db:seed
```

### 6. Compilar Assets
```bash
# Desarrollo (con hot reload)
npm run dev

# Producción
npm run build
```

### 7. Iniciar Servidor
```bash
# Servidor de desarrollo Laravel
php artisan serve

# El sistema estará disponible en: http://localhost:8000
```

---

## ⚙️ Configuración

### Configuración de Correo (Opcional)

Para habilitar notificaciones por email, configurar en `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@clinicaeden.com
MAIL_FROM_NAME="Clínica Edén"
```

### Configuración de Storage
```bash
# Crear enlace simbólico para archivos públicos
php artisan storage:link
```

### Limpiar Cache (Opcional)
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## 📂 Estructura del Proyecto
```
clinicaeden/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 19 controladores (uno por módulo)
│   │   └── Middleware/         # Middleware de roles y autenticación
│   ├── Models/                 # 18 modelos Eloquent
│   ├── Livewire/              # 4 componentes Livewire (Pacientes)
│   ├── Notifications/         # 9 tipos de notificaciones
│   └── Providers/             # Service providers
├── database/
│   ├── migrations/            # 35 migraciones
│   ├── seeders/              # 4 seeders
│   └── factories/            # Factories para testing
├── resources/
│   ├── views/                # 105+ archivos Blade
│   │   ├── admin/           # Vistas administrativas
│   │   ├── medico_general/  # Panel médico general
│   │   ├── recepcion/       # Panel recepcionista
│   │   └── ...
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php              # Rutas principales (~100+)
│   └── api.php              # API endpoints
├── tests/
│   ├── Feature/             # 27 pruebas funcionales
│   └── Unit/               # 1 prueba unitaria
├── docs/                    # Documentación del proyecto
└── public/                 # Assets públicos
```

---

## 👥 Roles y Permisos

El sistema implementa 7 roles con accesos específicos:

### 1. 🔧 Administrador (`admin`)
- Gestión completa de usuarios (CRUD)
- Configuración de estructura hospitalaria
- Asignación de personal médico y de enfermería
- Acceso a todos los reportes y estadísticas
- Gestión de facturación

### 2. 📝 Recepcionista (`recepcionista`)
- Registro y gestión de pacientes
- Derivación de pacientes a médicos
- Procesamiento de salidas
- Aplicación de encuestas de satisfacción

### 3. 👨‍⚕️ Médico General (`medico_general`)
- Atención de consultas médicas
- Creación de historias clínicas
- Prescripción de fórmulas médicas
- Solicitud de hospitalizaciones
- Registro de signos vitales

### 4. 🩺 Médico Especialista (`medico_especialista`)
- Atención de interconsultas
- Gestión de pacientes especializados
- Autorización de altas médicas
- Reportes especializados

### 5. 👩‍⚕️ Jefe de Enfermería (`jefe_enfermeria`)
- Supervisión de hospitalizaciones
- Asignación de auxiliares a pacientes
- Revisión y aprobación de tratamientos
- Gestión de módulos de enfermería
- Autorización de altas de enfermería

### 6. 🏥 Auxiliar de Enfermería (`auxiliar_enfermeria`)
- Ejecución de tratamientos asignados
- Registro de procedimientos
- Seguimiento de pacientes hospitalizados
- Comentarios sobre evolución

### 7. 💵 Caja (`caja`)
- Búsqueda y visualización de facturas
- Procesamiento de pagos
- Confirmación de transacciones
- Reportes de ingresos
- Cierre de caja

---

## 🧩 Módulos del Sistema

### 📋 Gestión de Pacientes
- CRUD completo de pacientes
- Historias clínicas digitales
- Búsqueda avanzada (cédula, nombre, historia clínica)
- Derivación entre especialidades

### 🏥 Gestión Hospitalaria
- **Estructura Jerárquica**: Pisos → Módulos → Habitaciones
- Asignación de habitaciones a pacientes
- Control de disponibilidad en tiempo real
- Gestión de consultorios y salas

### 👨‍⚕️ Consultas Médicas
- Registro de consultas por médico
- Captura de signos vitales
- Diagnósticos y observaciones
- Fórmulas médicas digitales
- Solicitud de interconsultas

### 🛏️ Hospitalización
- Admisión de pacientes
- Asignación de personal de enfermería
- Registro de tratamientos y procedimientos
- Seguimiento diario
- Proceso de alta (médica + enfermería)

### 💰 Facturación
- Generación automática de facturas
- Cálculo de costos (consultas + tratamientos + procedimientos)
- Procesamiento de pagos
- Estados: Pendiente, Pagada, Anulada
- Reportes financieros

### 🔔 Notificaciones
- Citas agendadas
- Nuevas interconsultas
- Órdenes médicas creadas
- Tratamientos asignados
- Solicitudes de autorización
- Pagos confirmados

### 📊 Reportes y Estadísticas
- Balance de personal por módulo
- Ingresos por período
- Estadísticas médicas
- Encuestas de satisfacción
- Reportes personalizados por rol

---

## 🧪 Pruebas

### Ejecutar Suite de Pruebas
```bash
# Todas las pruebas
php artisan test

# Con cobertura
php artisan test --coverage

# Pruebas específicas
php artisan test --filter=AdminTest
```

### Cobertura Actual

- **Feature Tests**: 27 pruebas
  - Admin: Gestión de usuarios, validaciones
  - Autenticación: Login, registro, 2FA
  - Caja: Procesamiento de pagos
  - Consultas: Atención médica
  - Pacientes: CRUD, derivaciones
  - Recepción: Salidas de pacientes

- **Unit Tests**: 1 prueba (ejemplo)

---

## 🔑 Credenciales de Demo

Después de ejecutar los seeders, podrás acceder con:

### Administrador
- **Email**: admin@clinicaeden.com
- **Contraseña**: password

### Recepcionista
- **Email**: recepcionista@clinicaeden.com
- **Contraseña**: password

### Médico General
- **Email**: medico.general@clinicaeden.com
- **Contraseña**: password

### Médico Especialista
- **Email**: medico.especialista@clinicaeden.com
- **Contraseña**: password

### Jefe de Enfermería
- **Email**: jefe.enfermeria@clinicaeden.com
- **Contraseña**: password

### Auxiliar de Enfermería
- **Email**: auxiliar.enfermeria@clinicaeden.com
- **Contraseña**: password

### Caja
- **Email**: caja@clinicaeden.com
- **Contraseña**: password

> ⚠️ **Nota de Seguridad**: Cambiar todas las contraseñas en producción

---

## 📚 Documentación Adicional

Para más información detallada, consultar:

- **Auditoría del Proyecto**: `docs/project_structure.txt` - Análisis completo de la estructura
- **Migraciones**: `database/migrations/` - Esquema de base de datos
- **Seeders**: `database/seeders/` - Datos de ejemplo

---

## 🗺️ Roadmap

### Versión 2.1 (Q1 2026)
- [ ] API RESTful completa con documentación Swagger
- [ ] Dashboard de analíticas avanzadas con Chart.js
- [ ] Módulo de inventario médico (medicamentos, insumos)
- [ ] Integración con pasarelas de pago (Wompi, PayU)

### Versión 2.2 (Q2 2026)
- [ ] Telemedicina (videoconsultas con WebRTC)
- [ ] App móvil nativa (React Native o Flutter)
- [ ] Integración con laboratorios externos
- [ ] Sistema de citas online para pacientes

### Versión 3.0 (Q3 2026)
- [ ] Multi-tenancy (SaaS para múltiples clínicas)
- [ ] Inteligencia artificial para sugerencias de diagnóstico
- [ ] Firma digital de documentos médicos
- [ ] Cumplimiento HIPAA/GDPR

---

## 🤝 Contribución

Este es un proyecto personal de portfolio. Si encontrás bugs o tenés sugerencias:

1. Abrí un **Issue** describiendo el problema o mejora
2. Si querés contribuir código:
   - Fork del repositorio
   - Creá una rama feature (`git checkout -b feature/nueva-funcionalidad`)
   - Commit de cambios (`git commit -m 'feat: agregar nueva funcionalidad'`)
   - Push a la rama (`git push origin feature/nueva-funcionalidad`)
   - Abrí un Pull Request

### Convención de Commits

Usamos [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` Nueva funcionalidad
- `fix:` Corrección de bugs
- `docs:` Cambios en documentación
- `style:` Cambios de formato (no afectan código)
- `refactor:` Refactorización de código
- `test:` Agregar o modificar pruebas
- `chore:` Tareas de mantenimiento

---

## 📄 Licencia

Este proyecto es de código abierto bajo la licencia **MIT License**.
```
MIT License

Copyright (c) 2025 Alirio Ing

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
```

---

## 📧 Contacto

**Alirio Portilla** - Full-Stack Developer

- 💼 **LinkedIn**: [linkedin.com/in/alirio-developer](https://linkedin.com/in/tu-perfil)
- 🐙 **GitHub**: [@DarbIng-ops](https://github.com/DarbIng-ops)
- 📧 **Email**: alirio1127portilla@gmail.com

---

<p align="center">
  Desarrollado con ❤️ en Uruguay 🇺🇾<br>
  <strong>Clínica Edén</strong> - Sistema de Gestión Hospitalaria<br>
  © 2025 Alirio Ing
</p>

---

## ⭐ ¿Te gustó el proyecto?

Si este proyecto te pareció útil o interesante:

- Dale una ⭐ en GitHub
- Compartilo con otros desarrolladores
- Seguime en [LinkedIn](https://linkedin.com/in/tu-perfil) para más proyectos

**¡Gracias por tu interés!** 🚀

## Licenciamiento

Este proyecto opera bajo licencia dual:

- **GPL v3** — libre para uso educativo y no comercial.
  Ver archivo [LICENSE](./LICENSE).
- **Licencia Comercial** — para implementaciones comerciales sin obligación
  de revelar código fuente modificado.
  Contacto: **alirioportilla96@gmail.com**

© 2026 Alirio Portilla. Todos los derechos reservados sobre la marca ClinicaEden.
