@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Panel Auxiliar de Enfermería</h1>
        <p class="text-gray-600">Tratamientos asignados y pacientes</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pacientes Asignados</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pacientesAsignados->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tratamientos Asignados</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $tratamientosAsignados->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido específico -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pacientes Asignados</h3>
        <div class="space-y-4">
            @forelse($pacientesAsignados as $hospitalizacion)
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $hospitalizacion->paciente->nombre_completo }}</h4>
                        <p class="text-sm text-gray-600">Habitación: {{ $hospitalizacion->habitacion->numero }}</p>
                        <p class="text-sm text-gray-600">Jefe: {{ $hospitalizacion->jefeEnfermeria->name ?? 'Sin asignar' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($hospitalizacion->estado) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-gray-500">No hay pacientes asignados</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
