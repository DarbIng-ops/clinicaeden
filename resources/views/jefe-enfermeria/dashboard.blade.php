@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Jefe de Enfermería</h1>
        <p class="text-gray-600">Gestión de módulos y personal de enfermería</p>
    </div>

    <!-- Estadísticas generales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Habitaciones</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalHabitaciones }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Total Camas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalCamas }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Camas Ocupadas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $camasOcupadas }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Camas Disponibles</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $camasDisponibles }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulos asignados -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Módulos Asignados</h3>
            </div>
            <div class="p-6">
                @forelse($modulos as $modulo)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $modulo->nombre }}</h4>
                            <p class="text-sm text-gray-600">Piso {{ $modulo->piso->numero }} - {{ ucfirst(str_replace('_', ' ', $modulo->tipo)) }}</p>
                            <p class="text-sm text-gray-500">{{ $modulo->habitaciones->count() }} habitaciones, {{ $modulo->auxiliares->count() }} auxiliares</p>
                        </div>
                        <a href="{{ route('jefe-enfermeria.modulos.auxiliares', $modulo) }}" 
                           class="text-blue-600 hover:text-blue-900 text-sm">Ver Auxiliares</a>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">No tienes módulos asignados</p>
                @endforelse
            </div>
        </div>

        <!-- Tratamientos pendientes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tratamientos Pendientes</h3>
            </div>
            <div class="p-6">
                @forelse($tratamientosPendientes as $tratamiento)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $tratamiento->nombre }}</h4>
                            <p class="text-sm text-gray-600">{{ $tratamiento->hospitalizacion->paciente->nombre_completo }}</p>
                            <p class="text-sm text-gray-500">Habitación {{ $tratamiento->hospitalizacion->habitacion->numero }}</p>
                        </div>
                        <form method="POST" action="{{ route('jefe-enfermeria.tratamientos.revisar', $tratamiento) }}" class="inline">
                            @csrf
                            <select name="estado" class="text-sm border border-gray-300 rounded px-2 py-1" onchange="this.form.submit()">
                                <option value="">Revisar...</option>
                                <option value="aprobado">Aprobar</option>
                                <option value="rechazado">Rechazar</option>
                                <option value="modificado">Modificar</option>
                            </select>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">No hay tratamientos pendientes de revisión</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Hospitalizaciones activas -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Hospitalizaciones Activas</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Habitación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auxiliar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Ingreso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($hospitalizaciones as $hospitalizacion)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->nombre_completo }}</div>
                            <div class="text-sm text-gray-500">{{ $hospitalizacion->paciente->dni }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $hospitalizacion->habitacion->numero }} ({{ $hospitalizacion->habitacion->modulo->nombre }})
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $hospitalizacion->medicoGeneral->nombre_completo }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($hospitalizacion->auxiliarEnfermeria)
                                {{ $hospitalizacion->auxiliarEnfermeria->nombre_completo }}
                            @else
                                <span class="text-red-500">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $hospitalizacion->fecha_ingreso->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('jefe-enfermeria.hospitalizaciones.ver', $hospitalizacion) }}" 
                                   class="text-blue-600 hover:text-blue-900">Ver</a>
                                @if(!$hospitalizacion->auxiliarEnfermeria)
                                    <button onclick="asignarAuxiliar({{ $hospitalizacion->id }})" 
                                            class="text-green-600 hover:text-green-900">Asignar Auxiliar</button>
                                @endif
                                <button onclick="darAltaEnfermeria({{ $hospitalizacion->id }})" 
                                        class="text-purple-600 hover:text-purple-900">Alta Enfermería</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay hospitalizaciones activas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para asignar auxiliar -->
<div id="modalAsignarAuxiliar" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Asignar Auxiliar</h3>
            <form id="formAsignarAuxiliar" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Auxiliar</label>
                    <select name="auxiliar_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar auxiliar...</option>
                        <!-- Aquí se cargarían los auxiliares disponibles -->
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="cerrarModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function asignarAuxiliar(hospitalizacionId) {
    document.getElementById('formAsignarAuxiliar').action = `/jefe-enfermeria/hospitalizaciones/${hospitalizacionId}/asignar-auxiliar`;
    document.getElementById('modalAsignarAuxiliar').classList.remove('hidden');
}

function darAltaEnfermeria(hospitalizacionId) {
    if (confirm('¿Estás seguro de dar el alta de enfermería?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/jefe-enfermeria/hospitalizaciones/${hospitalizacionId}/alta-enfermeria`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function cerrarModal() {
    document.getElementById('modalAsignarAuxiliar').classList.add('hidden');
}
</script>
@endsection
