@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Asignación de Jefes de Enfermería</h1>
            <p class="text-gray-600">Gestiona la asignación de jefes de enfermería a módulos</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            Volver al Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Lista de jefes de enfermería -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-teal-50">
                <h2 class="text-lg font-semibold text-teal-900">Jefes de Enfermería Activos</h2>
                <p class="text-sm text-teal-700">{{ $jefes->count() }} jefes disponibles</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($jefes as $jefe)
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-teal-700 font-bold text-sm">{{ strtoupper(substr($jefe->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $jefe->name }} {{ $jefe->apellido }}</p>
                        <p class="text-xs text-gray-400">{{ $jefe->email }}</p>
                        @if($jefe->modulosComoJefe && $jefe->modulosComoJefe->count() > 0)
                            <p class="text-xs text-teal-600 mt-1">
                                Módulos asignados: {{ $jefe->modulosComoJefe->pluck('nombre')->join(', ') }}
                            </p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Sin módulo asignado</p>
                        @endif
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <p>No hay jefes de enfermería activos</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Lista de módulos -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
                <h2 class="text-lg font-semibold text-purple-900">Módulos de Enfermería</h2>
                <p class="text-sm text-purple-700">{{ $modulos->count() }} módulos activos</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($modulos as $modulo)
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $modulo->nombre }}</p>
                            @if($modulo->piso)
                                <p class="text-sm text-gray-500">Piso {{ $modulo->piso->numero ?? $modulo->piso->nombre ?? '-' }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($modulo->jefeEnfermeria)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">
                                    {{ $modulo->jefeEnfermeria->name }} {{ $modulo->jefeEnfermeria->apellido }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                    Sin jefe asignado
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <p>No hay módulos de enfermería activos</p>
                    <a href="{{ route('modulos.create') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                        Crear módulo
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Panel de gestión -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Gestión de Módulos</h2>
        <p class="text-sm text-gray-600 mb-4">
            Para asignar o cambiar el jefe de un módulo, ve a la sección de módulos donde podrás editarlos individualmente.
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('modulos.index') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition text-sm">
                Gestionar módulos
            </a>
            <a href="{{ route('admin.usuarios.crear') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm">
                Crear jefe de enfermería
            </a>
            <a href="{{ route('admin.usuarios') }}?rol=jefe_enfermeria" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm">
                Ver todos los jefes
            </a>
        </div>
    </div>
</div>
@endsection
