@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Asignación de Médicos</h1>
            <p class="text-gray-600">Gestiona la asignación de médicos a módulos de enfermería</p>
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
        <!-- Lista de médicos -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                <h2 class="text-lg font-semibold text-blue-900">Médicos Activos</h2>
                <p class="text-sm text-blue-700">{{ $medicos->count() }} médicos disponibles</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($medicos as $medico)
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-700 font-bold text-sm">{{ strtoupper(substr($medico->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $medico->name }} {{ $medico->apellido }}</p>
                        <p class="text-sm text-gray-500">{{ $medico->especialidad ?? 'Sin especialidad' }}</p>
                        <p class="text-xs text-gray-400">{{ $medico->email }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <p>No hay médicos con especialidad activos</p>
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
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Jefe: {{ $modulo->jefeEnfermeria->name }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
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

    <!-- Acciones rápidas -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.usuarios.crear') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                Crear nuevo médico
            </a>
            <a href="{{ route('admin.usuarios') }}?rol=medico_general" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm">
                Ver todos los médicos generales
            </a>
            <a href="{{ route('admin.usuarios') }}?rol=medico_especialista" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm">
                Ver todos los especialistas
            </a>
        </div>
    </div>
</div>
@endsection
