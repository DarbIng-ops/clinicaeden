@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Panel Jefe de Enfermería</h1>
        <p class="text-gray-600">Supervisión de tratamientos y equipo</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pacientes en Supervisión</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pacientesSupervision->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Auxiliares</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $auxiliares->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tratamientos Pendientes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $tratamientosPendientes->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido específico -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pacientes bajo Supervisión</h3>
        <div class="space-y-4">
            @forelse($pacientesSupervision as $hospitalizacion)
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $hospitalizacion->paciente->nombre_completo }}</h4>
                        <p class="text-sm text-gray-600">Habitación: {{ $hospitalizacion->habitacion->numero }}</p>
                        <p class="text-sm text-gray-600">Auxiliar: {{ $hospitalizacion->auxiliarEnfermeria->name ?? 'Sin asignar' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($hospitalizacion->estado) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-gray-500">No hay pacientes bajo supervisión</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
