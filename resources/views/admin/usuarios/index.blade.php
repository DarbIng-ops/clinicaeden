@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Gestión de Usuarios</h1>
        <p class="text-gray-600">Administrar usuarios del sistema</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.usuarios') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nombre, email, DNI..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                <select name="rol" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('rol') == $role ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="activo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Botón crear usuario -->
    <div class="mb-6">
        <a href="{{ route('admin.usuarios.crear') }}" 
           class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
            Crear Nuevo Usuario
        </a>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($usuarios as $usuario)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" 
                                     src="{{ $usuario->profile_photo_url }}" 
                                     alt="{{ $usuario->nombre_completo }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $usuario->nombre_completo }}</div>
                                <div class="text-sm text-gray-500">{{ $usuario->dni }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $usuario->role === 'admin' ? 'bg-red-100 text-red-800' : 
                               ($usuario->role === 'medico_general' || $usuario->role === 'medico_especialista' ? 'bg-blue-100 text-blue-800' : 
                               ($usuario->role === 'jefe_enfermeria' ? 'bg-purple-100 text-purple-800' : 
                               ($usuario->role === 'auxiliar_enfermeria' ? 'bg-green-100 text-green-800' : 
                               ($usuario->role === 'recepcionista' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-gray-100 text-gray-800')))) }}">
                            {{ ucfirst(str_replace('_', ' ', $usuario->role)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>{{ $usuario->email }}</div>
                        <div class="text-gray-500">{{ $usuario->telefono }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $usuario->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.usuarios.ver', $usuario) }}" 
                               class="text-blue-600 hover:text-blue-900">Ver</a>
                            <a href="{{ route('admin.usuarios.editar', $usuario) }}" 
                               class="text-indigo-600 hover:text-indigo-900">Editar</a>
                            @if($usuario->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.usuarios.eliminar', $usuario) }}" 
                                      class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No se encontraron usuarios
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $usuarios->links() }}
    </div>

    <!-- Usuarios Inactivos / Eliminados -->
    <div class="mt-8 bg-gray-50 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">🗂️ Historial de Usuarios</h2>
                <p class="text-sm text-gray-600 mt-1">Usuarios que ya no están activos en el sistema</p>
            </div>
            <button onclick="toggleHistorial()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                <span id="toggle-text">Mostrar Historial</span>
            </button>
        </div>
        
        <div id="historial-usuarios" class="hidden">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Baja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($usuariosInactivos as $usuario)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-white font-semibold">
                                        {{ substr($usuario->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $usuario->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $usuario->dni ?? 'Sin DNI' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                    {{ ucwords(str_replace('_', ' ', $usuario->role)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $usuario->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $usuario->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form action="{{ route('admin.usuarios.reactivar', $usuario->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            onclick="return confirm('¿Está seguro de reactivar a {{ $usuario->name }}?')"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                                        🔄 Reactivar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No hay usuarios inactivos
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($usuariosInactivos->count() > 0)
            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-sm text-yellow-700">
                    <strong>ℹ️ Nota:</strong> Los usuarios inactivos no pueden iniciar sesión. 
                    Al reactivarlos, recuperarán acceso al sistema con sus credenciales anteriores.
                </p>
            </div>
            @endif
        </div>
    </div>

    <script>
    function toggleHistorial() {
        const historial = document.getElementById('historial-usuarios');
        const toggleText = document.getElementById('toggle-text');
        
        if (historial.classList.contains('hidden')) {
            historial.classList.remove('hidden');
            toggleText.textContent = 'Ocultar Historial';
        } else {
            historial.classList.add('hidden');
            toggleText.textContent = 'Mostrar Historial';
        }
    }
    </script>
</div>
@endsection
