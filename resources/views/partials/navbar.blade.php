<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Inicio</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        
        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="badge badge-danger navbar-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    {{ auth()->user()->unreadNotifications->count() }} Notificaciones
                </span>
                
                <div class="dropdown-divider"></div>
                
                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                    <a href="{{ $notification->data['url'] ?? '#' }}" 
                       class="dropdown-item" 
                       onclick="marcarComoLeida('{{ $notification->id }}')">
                        <i class="fas fa-envelope mr-2"></i> 
                        {{ Str::limit($notification->data['mensaje'] ?? 'Notificación', 50) }}
                        <span class="float-right text-muted text-sm">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </a>
                    <div class="dropdown-divider"></div>
                @empty
                    <span class="dropdown-item text-center text-muted">No hay notificaciones</span>
                    <div class="dropdown-divider"></div>
                @endforelse
                
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <a href="{{ route('notificaciones.index') }}" class="dropdown-item dropdown-footer">
                        Ver todas las notificaciones
                    </a>
                @endif
            </div>
        </li>

        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
                <span class="ml-1">{{ auth()->user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ auth()->user()->email }}</span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile.show') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Mi Perfil
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>

@push('scripts')
<script>
function marcarComoLeida(notificationId) {
    fetch(`/notificaciones/${notificationId}/marcar-leida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    }).then(() => {
        // Recargar la página para actualizar el contador
        location.reload();
    });
}
</script>
@endpush