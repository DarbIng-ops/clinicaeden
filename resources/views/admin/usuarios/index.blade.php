@extends('layouts.adminlte')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search mr-1"></i> Filtros de búsqueda</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.usuarios') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Buscar</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Nombre, email, DNI..."
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Rol</label>
                            <select name="rol" class="form-control">
                                <option value="">Todos los roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ request('rol') == $role ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="activo" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-1"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla principal de usuarios -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users mr-1"></i> Usuarios del Sistema</h3>
            <div class="card-tools">
                <a href="{{ route('admin.usuarios.crear') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus mr-1"></i> Nuevo Usuario
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $usuario->profile_photo_url }}"
                                     alt="{{ $usuario->nombre_completo }}"
                                     class="img-circle mr-2"
                                     style="width:38px;height:38px;object-fit:cover;">
                                <div>
                                    <div class="font-weight-bold">{{ $usuario->nombre_completo }}</div>
                                    <small class="text-muted">{{ $usuario->dni ?? '—' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $roleColor = match($usuario->role) {
                                    'admin'                 => 'danger',
                                    'medico_general',
                                    'medico_especialista'   => 'info',
                                    'jefe_enfermeria'       => 'warning',
                                    'auxiliar_enfermeria'   => 'success',
                                    'recepcionista'         => 'secondary',
                                    default                 => 'light',
                                };
                            @endphp
                            <span class="badge badge-{{ $roleColor }}">
                                {{ ucfirst(str_replace('_', ' ', $usuario->role)) }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $usuario->email }}</div>
                            <small class="text-muted">{{ $usuario->telefono ?? '—' }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $usuario->activo ? 'success' : 'danger' }}">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.usuarios.ver', $usuario) }}"
                               class="btn btn-info btn-xs" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.usuarios.editar', $usuario) }}"
                               class="btn btn-warning btn-xs" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($usuario->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.usuarios.eliminar', $usuario) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Eliminar a {{ addslashes($usuario->nombre_completo) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                            No se encontraron usuarios
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $usuarios->links() }}
        </div>
    </div>

    <!-- Historial de Usuarios Inactivos -->
    <div class="card card-secondary collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-archive mr-1"></i> Historial de Usuarios Inactivos
                @if($usuariosInactivos->count() > 0)
                    <span class="badge badge-warning ml-1">{{ $usuariosInactivos->count() }}</span>
                @endif
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Email</th>
                        <th>Fecha Baja</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuariosInactivos as $usuario)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="img-circle bg-secondary d-flex align-items-center justify-content-center mr-2 text-white font-weight-bold"
                                     style="width:38px;height:38px;font-size:16px;flex-shrink:0;">
                                    {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $usuario->name }}</div>
                                    <small class="text-muted">{{ $usuario->dni ?? 'Sin DNI' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">
                                {{ ucwords(str_replace('_', ' ', $usuario->role)) }}
                            </span>
                        </td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->updated_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <form action="{{ route('admin.usuarios.reactivar', $usuario->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        onclick="return confirm('¿Reactivar a {{ addslashes($usuario->name) }}?')"
                                        class="btn btn-success btn-xs">
                                    <i class="fas fa-undo mr-1"></i> Reactivar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No hay usuarios inactivos
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($usuariosInactivos->count() > 0)
        <div class="card-footer bg-warning">
            <i class="fas fa-info-circle mr-1"></i>
            Los usuarios inactivos no pueden iniciar sesión. Al reactivarlos, recuperarán acceso con sus credenciales anteriores.
        </div>
        @endif
    </div>

@endsection
