@extends('layouts.adminlte')

@section('content')
<div>
    <x-banner-bienvenida
        nombre="Recepcionista"
        subtitulo="Gestiona el registro y salida de pacientes del Sanatorio"
        emoji="🏥"
    />

    <!-- Estadísticas del día -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users fa-2x text-blue-400"></i>
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
                    <i class="fas fa-exclamation-triangle fa-2x" style="color:#f59e0b"></i>
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
                    <i class="fas fa-file-invoice fa-2x" style="color:#f87171"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Facturas Pendientes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $facturasPendientes }}</p>
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
                                    {{ $factura->fecha_pago ? $factura->fecha_pago->format('H:i') : '--:--' }}
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('recepcion.pacientes.index') }}" class="flex flex-col items-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors shadow-sm">
                <i class="fas fa-user-plus fa-3x text-blue-500 mb-2"></i>
                <span class="text-lg font-semibold text-blue-900">Gestión de Pacientes</span>
                <span class="text-sm text-blue-700 mt-1">Registrar, editar y gestionar pacientes</span>
            </a>

            <a href="{{ route('recepcion.salidas') }}" class="flex flex-col items-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors shadow-sm">
                <i class="fas fa-sign-out-alt fa-3x text-green-500 mb-2"></i>
                <span class="text-lg font-semibold text-green-900">Procesar Salidas</span>
                <span class="text-sm text-green-700 mt-1">Gestionar salida de pacientes</span>
            </a>

            <a href="#historial-pacientes"
               class="block p-6 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors text-center">
                <i class="fas fa-book-medical fa-3x mb-2" style="color:#d97706"></i>
                <p class="font-semibold text-amber-700">Base de Pacientes</p>
                <p class="text-xs text-amber-600 mt-1">Historial completo — reactivar pacientes previos</p>
            </a>
        </div>
    </div>

    <!-- Base de Pacientes Registrados -->
    <div id="historial-pacientes" class="mt-6 bg-white rounded-xl border border-gray-200 p-6">
        @livewire('recepcion.historial-pacientes')
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
