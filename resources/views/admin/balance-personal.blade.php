@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Balance de Personal</h1>
            <p class="text-gray-600">Distribución del personal activo por rol y módulo</p>
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

    <!-- Resumen por rol -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @php
        $rolesLabels = [
            'admin' => ['label' => 'Administradores', 'color' => 'blue'],
            'recepcionista' => ['label' => 'Recepcionistas', 'color' => 'green'],
            'medico_general' => ['label' => 'Médicos Generales', 'color' => 'purple'],
            'medico_especialista' => ['label' => 'Especialistas', 'color' => 'indigo'],
            'jefe_enfermeria' => ['label' => 'Jefes Enfermería', 'color' => 'teal'],
            'auxiliar_enfermeria' => ['label' => 'Auxiliares', 'color' => 'orange'],
            'caja' => ['label' => 'Caja', 'color' => 'yellow'],
        ];
        @endphp
        @foreach($balancePersonal as $rol => $total)
        @php $info = $rolesLabels[$rol] ?? ['label' => ucfirst($rol), 'color' => 'gray']; @endphp
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $info['label'] }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
                </div>
                <div class="p-3 rounded-full bg-{{ $info['color'] }}-100">
                    <svg class="w-6 h-6 text-{{ $info['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Total -->
        <div class="bg-gray-800 rounded-lg shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total Personal</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $balancePersonal->sum() }}</p>
                </div>
                <div class="p-3 rounded-full bg-gray-700">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal por módulo -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Personal por Módulo de Enfermería</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Módulo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Piso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jefe de Enfermería</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auxiliares</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($personalPorModulo as $dato)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-900">{{ $dato['modulo'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            Piso {{ $dato['piso'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($dato['jefe'] !== 'Sin asignar')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">
                                    {{ $dato['jefe'] }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(count($dato['auxiliares_nombres']) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($dato['auxiliares_nombres'] as $nombre)
                                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">{{ $nombre }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Sin auxiliares</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800">
                                {{ $dato['auxiliares'] + ($dato['jefe'] !== 'Sin asignar' ? 1 : 0) }} personas
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No hay módulos de enfermería configurados.
                            <a href="{{ route('modulos.create') }}" class="text-blue-600 hover:underline ml-1">Crear módulo</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        nav, button { display: none !important; }
        .shadow { box-shadow: none !important; }
    }
</style>
@endpush
@endsection
