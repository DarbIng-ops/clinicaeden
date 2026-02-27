@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Consultas</h1>
            <p class="text-gray-600">Historial completo de consultas asignadas</p>
        </div>
        <a href="{{ route('medico_general.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            Volver al Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Todas las consultas
                <span class="text-sm font-normal text-gray-500 ml-2">({{ $consultas->total() }} registros)</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($consultas as $consulta)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $consulta->paciente->nombre_completo }}</div>
                            <div class="text-xs text-gray-500">DNI: {{ $consulta->paciente->dni }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                            {{ $consulta->motivo_consulta ?? $consulta->motivo ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $consulta->fecha_consulta->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($consulta->estado === 'pendiente')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                            @elseif($consulta->estado === 'completada')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completada</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($consulta->estado) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($consulta->estado === 'pendiente')
                                <a href="{{ route('medico_general.consultas.atender', $consulta) }}"
                                   class="text-blue-600 hover:text-blue-900">Atender</a>
                            @else
                                <span class="text-gray-400">Completada</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No tienes consultas registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($consultas->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $consultas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
