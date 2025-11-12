@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Gestión de Pacientes</h1>
        <div class="space-x-2">
            <a href="{{ route('recepcion.pacientes.crear') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Nuevo Paciente
            </a>
        </div>
    </div>

    <!-- Acceso Rápido a Salidas -->
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-8 w-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">🚪 Pacientes Listos para Salida</h3>
                    <p class="text-sm text-gray-600">Procesar salida de pacientes que completaron su pago en caja</p>
                </div>
            </div>
            <a href="{{ route('recepcion.salidas') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition inline-flex items-center shadow-md">
                Ver Salidas Pendientes
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('recepcion.pacientes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="DNI, nombre o apellido">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sexo</label>
                <select name="sexo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="M" {{ request('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ request('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="Otro" {{ request('sexo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
                <input type="text" name="ciudad" value="{{ request('ciudad') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="Ciudad">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>

    <!-- Mensajes de éxito/error -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabla de pacientes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($pacientes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Sangre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pacientes as $paciente)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $paciente->dni }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($paciente->foto)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $paciente->foto) }}" alt="{{ $paciente->nombre_completo }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $paciente->nombre_completo }}</div>
                                            <div class="text-sm text-gray-500">{{ $paciente->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $paciente->edad }} años
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $paciente->telefono }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $paciente->ciudad }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($paciente->tipo_sangre)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $paciente->tipo_sangre }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">No especificado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('recepcion.pacientes.derivar', $paciente->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                           Derivar a Médico
                                        </a>
                                        <a href="{{ route('recepcion.pacientes.show', $paciente) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('recepcion.pacientes.edit', $paciente) }}" 
                                           class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('recepcion.pacientes.destroy', $paciente) }}" 
                                              class="inline" onsubmit="return confirm('¿Está seguro de que desea eliminar este paciente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $pacientes->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay pacientes registrados</h3>
                <p class="text-gray-500 mb-6">Comience registrando el primer paciente del sistema.</p>
                <a href="{{ route('recepcion.pacientes.crear') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Registrar Primer Paciente
                </a>
            </div>
        @endif
    </div>
</div>
@endsection