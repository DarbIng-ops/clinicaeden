<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <i class="fas fa-hospital-alt brand-image ml-3"></i>
        <span class="brand-text font-weight-light">ClinicaEden</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="info">
                <a href="{{ route('profile.show') }}" class="d-block">{{ auth()->user()->name }}</a>
                <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                @if(auth()->user()->role === 'admin')
                    <!-- Menú Administrador -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Médicos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Configuración</p>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->role === 'medico_general')
                    <!-- Menú Médico General -->
                    <li class="nav-item">
                        <a href="{{ route('medico_general.dashboard') }}" class="nav-link {{ request()->routeIs('medico_general.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('medico_general.pacientes.index') }}" class="nav-link {{ request()->routeIs('medico_general.pacientes.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-injured"></i>
                            <p>Mis Pacientes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('medico_general.consultas.index') }}" class="nav-link {{ request()->routeIs('medico_general.consultas.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-notes-medical"></i>
                            <p>Consultas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('hospitalizaciones.index') }}" class="nav-link {{ request()->routeIs('hospitalizaciones.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-bed"></i>
                            <p>Hospitalizaciones</p>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->role === 'medico_especialista')
                    <!-- Menú Médico Especialista -->
                    <li class="nav-item">
                        <a href="{{ route('medico_especialista.dashboard') }}" class="nav-link {{ request()->routeIs('medico_especialista.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('medico_especialista.pacientes.index') }}" class="nav-link {{ request()->routeIs('medico_especialista.pacientes.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-injured"></i>
                            <p>Mis Pacientes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('medico_especialista.consultas.index') }}" class="nav-link {{ request()->routeIs('medico_especialista.consultas.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-notes-medical"></i>
                            <p>Consultas Especializadas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('medico_especialista.estadisticas') }}" class="nav-link {{ request()->routeIs('medico_especialista.estadisticas') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Estadísticas</p>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->role === 'recepcionista')
    <!-- Menú Recepcionista -->
    <li class="nav-item">
        <a href="{{ route('recepcion.dashboard') }}" class="nav-link {{ request()->routeIs('recepcion.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('recepcion.pacientes.index') }}" class="nav-link {{ request()->routeIs('recepcion.pacientes.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-users"></i>
            <p>Pacientes</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-calendar-alt"></i>
            <p>Agendar Cita</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>Ver Citas</p>
        </a>
    </li>
@endif

            </ul>
        </nav>
    </div>
</aside>