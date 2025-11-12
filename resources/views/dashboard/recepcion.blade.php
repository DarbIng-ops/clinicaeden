@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Panel de Recepción</h1>
        <p class="text-gray-600">Gestión de pacientes y altas</p>
    </div>

    <!-- Estadísticas del día -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pacientes Hoy</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pacientesHoy }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Pendientes de Alta</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $hospitalizacionesPendientes }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Facturas Pendientes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $facturasPendientes }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Encuestas Pendientes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $encuestasPendientes }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pacientes Listos para Salida -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">
                🚪 Pacientes Listos para Salida
            </h2>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $pacientesListosParaSalida->count() }} pendientes
            </span>
        </div>

        @if($pacientesListosParaSalida->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DNI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Pagado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora Pago</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pacientesListosParaSalida as $factura)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $factura->paciente->nombres }} {{ $factura->paciente->apellidos }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $factura->paciente->dni }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $factura->numero_factura }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                    ${{ number_format($factura->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $factura->fecha_pago->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('recepcion.pacientes.salida', $factura->paciente->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                        ✓ Dar Salida
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2">No hay pacientes pendientes de salida</p>
            </div>
        @endif
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('recepcion.pacientes.index') }}" class="flex flex-col items-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors shadow-sm">
                <svg class="w-12 h-12 text-blue-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-lg font-semibold text-blue-900">Gestión de Pacientes</span>
                <span class="text-sm text-blue-700 mt-1">Registrar, editar y gestionar pacientes</span>
            </a>

            <a href="{{ route('recepcion.salidas') }}" class="flex flex-col items-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors shadow-sm">
                <svg class="w-12 h-12 text-green-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="text-lg font-semibold text-green-900">Procesar Salidas</span>
                <span class="text-sm text-green-700 mt-1">Gestionar salida de pacientes</span>
            </a>
        </div>
    </div>

    <!-- Notificaciones Recientes -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notificaciones Recientes</h3>
        <div class="space-y-3">
            @forelse($notificaciones as $notificacion)
            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $notificacion->titulo }}</p>
                    <p class="text-sm text-gray-600">{{ Str::limit($notificacion->mensaje, 100) }}</p>
                    <p class="text-xs text-gray-500">{{ $notificacion->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500">No hay notificaciones recientes</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
