@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reporte de Ingresos</h1>
            <p class="text-gray-600">Resumen de pagos recibidos en el período seleccionado</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Imprimir
            </button>
            <a href="{{ route('caja.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Volver
            </a>
        </div>
    </div>

    <!-- Filtros de fecha -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('caja.reportes.ingresos') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio"
                    value="{{ is_string($fechaInicio) ? $fechaInicio : (is_object($fechaInicio) ? $fechaInicio->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin"
                    value="{{ is_string($fechaFin) ? $fechaFin : (is_object($fechaFin) ? $fechaFin->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Ingresos</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($totalIngresos, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Facturas Pagadas</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $facturas->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Promedio por Factura</p>
                    <p class="text-2xl font-bold text-purple-600">
                        ${{ $facturas->count() > 0 ? number_format($totalIngresos / $facturas->count(), 2) : '0.00' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ingresos por método de pago -->
    @if($ingresosPorMetodo->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ingresos por Método de Pago</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($ingresosPorMetodo as $metodo => $monto)
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600 mb-1">{{ ucfirst($metodo ?? 'No especificado') }}</p>
                <p class="text-xl font-bold text-gray-900">${{ number_format($monto, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $totalIngresos > 0 ? number_format(($monto / $totalIngresos) * 100, 1) : 0 }}% del total
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tabla de facturas -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detalle de Facturas Pagadas</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Factura</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Pago</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($facturas as $factura)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $factura->numero_factura ?? str_pad($factura->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $factura->paciente->nombre_completo }}</div>
                            <div class="text-xs text-gray-500">DNI: {{ $factura->paciente->dni }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($factura->hospitalizacion) Hospitalización
                            @elseif($factura->consulta) Consulta
                            @else Servicio médico
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ ucfirst($factura->metodo_pago ?? '-') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $factura->fecha_pago ? \Carbon\Carbon::parse($factura->fecha_pago)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-gray-900">
                            ${{ number_format($factura->total, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No hay facturas pagadas en el período seleccionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($facturas->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-900">TOTAL:</td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-green-600">${{ number_format($totalIngresos, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        nav, .print-btn, form, button { display: none !important; }
        .shadow { box-shadow: none !important; }
    }
</style>
@endpush
@endsection
