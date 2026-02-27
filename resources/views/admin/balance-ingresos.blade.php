@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Balance de Ingresos</h1>
            <p class="text-gray-600">Resumen financiero y comparativo de ingresos</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Imprimir
            </button>
            <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Volver
            </a>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Ingresos Mes Actual</p>
            <p class="text-3xl font-bold text-green-600 mt-2">${{ number_format($ingresosMesActual, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Ingresos Mes Anterior</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">${{ number_format($ingresosMesAnterior, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ now()->subMonth()->format('F Y') }}</p>
            @php $variacion = $ingresosMesAnterior > 0 ? (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100 : 0; @endphp
            <p class="text-xs mt-2 {{ $variacion >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $variacion >= 0 ? '+' : '' }}{{ number_format($variacion, 1) }}% vs mes anterior
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Facturas Pendientes</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">${{ number_format($facturasPendientes, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Por cobrar</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Facturado</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($facturasPagadas, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Histórico acumulado</p>
        </div>
    </div>

    <!-- Ingresos por año -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Ingresos por Año</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ingresos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Participación</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $totalHistorico = $ingresosPorAno->sum('total'); @endphp
                    @forelse($ingresosPorAno as $ingreso)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-900">{{ $ingreso->año }}</span>
                            @if($ingreso->año == now()->year)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">Actual</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-green-600">
                            ${{ number_format($ingreso->total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @if($totalHistorico > 0)
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($ingreso->total / $totalHistorico) * 100) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ number_format(($ingreso->total / $totalHistorico) * 100, 1) }}%</span>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            No hay ingresos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($ingresosPorAno->isNotEmpty())
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-4 font-bold text-gray-900">TOTAL HISTÓRICO</td>
                        <td class="px-6 py-4 text-right font-bold text-green-600">${{ number_format($totalHistorico, 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-600">100%</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Estado de facturas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Estado de Facturas</h2>
            @php $totalGeneral = $facturasPagadas + $facturasPendientes; @endphp
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Pagadas</span>
                        <span class="font-semibold text-green-600">${{ number_format($facturasPagadas, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full" style="width: {{ $totalGeneral > 0 ? min(100, ($facturasPagadas / $totalGeneral) * 100) : 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $totalGeneral > 0 ? number_format(($facturasPagadas / $totalGeneral) * 100, 1) : 0 }}% del total
                    </p>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Pendientes</span>
                        <span class="font-semibold text-yellow-600">${{ number_format($facturasPendientes, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full" style="width: {{ $totalGeneral > 0 ? min(100, ($facturasPendientes / $totalGeneral) * 100) : 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $totalGeneral > 0 ? number_format(($facturasPendientes / $totalGeneral) * 100, 1) : 0 }}% del total
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.reportes') }}" class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-900">Ver reportes completos</span>
                </a>
                <a href="{{ route('facturas.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-green-50 hover:bg-green-100 transition">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-900">Gestionar facturas</span>
                </a>
                <a href="{{ route('admin.balance-personal') }}" class="flex items-center gap-3 p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-purple-900">Ver balance de personal</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        nav, button, a.bg-blue-600 { display: none !important; }
        .shadow { box-shadow: none !important; }
    }
</style>
@endpush
@endsection
