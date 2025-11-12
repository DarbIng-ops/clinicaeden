@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Auxiliar de Enfermería</h1>
        <p class="text-gray-600">Procedimientos asignados y atención a pacientes</p>
    </div>

    <!-- Estadísticas del día -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pacientes Asignados</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPacientesAsignados }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Procedimientos Pendientes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $procedimientosPendientesCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completados Hoy</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $procedimientosCompletadosHoy }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Módulos</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $modulos->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('auxiliar-enfermeria.procedimientos.index') }}" 
           class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Ver Procedimientos</h3>
                    <p class="text-gray-600">Lista completa de procedimientos asignados</p>
                </div>
            </div>
        </a>

        <a href="{{ route('auxiliar-enfermeria.modulos.index') }}" 
           class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Módulos Asignados</h3>
                    <p class="text-gray-600">Información de módulos de trabajo</p>
                </div>
            </div>
        </a>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Reportes</h3>
                    <p class="text-gray-600">Próximamente disponible</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hospitalizaciones asignadas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pacientes Asignados</h3>
            </div>
            <div class="p-6">
                @forelse($hospitalizacionesAsignadas as $hospitalizacion)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $hospitalizacion->paciente->nombre_completo }}</h4>
                            <p class="text-sm text-gray-600">Habitación {{ $hospitalizacion->habitacion->numero }} - {{ $hospitalizacion->habitacion->modulo->nombre }}</p>
                            <p class="text-sm text-gray-500">Ingreso: {{ $hospitalizacion->fecha_ingreso->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('auxiliar-enfermeria.hospitalizaciones.ver', $hospitalizacion) }}" 
                           class="text-blue-600 hover:text-blue-900 text-sm">Ver Detalles</a>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">No tienes pacientes asignados</p>
                @endforelse
            </div>
        </div>

        <!-- Procedimientos pendientes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Procedimientos Pendientes</h3>
            </div>
            <div class="p-6">
                @forelse($procedimientosPendientes as $procedimiento)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $procedimiento->nombre }}</h4>
                            <p class="text-sm text-gray-600">{{ $procedimiento->hospitalizacion->paciente->nombre_completo }}</p>
                            <p class="text-sm text-gray-500">Programado: {{ $procedimiento->fecha_programada->format('d/m/Y H:i') }}</p>
                        </div>
                        <button onclick="completarProcedimiento({{ $procedimiento->id }})" 
                                class="text-green-600 hover:text-green-900 text-sm">Completar</button>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">No hay procedimientos pendientes</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Módulos asignados -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Módulos de Trabajo</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($modulos as $modulo)
                <div class="p-4 border border-gray-200 rounded-lg">
                    <h4 class="font-semibold text-gray-900">{{ $modulo->nombre }}</h4>
                    <p class="text-sm text-gray-600">Piso {{ $modulo->piso->numero }} - {{ ucfirst(str_replace('_', ' ', $modulo->tipo)) }}</p>
                    <p class="text-sm text-gray-500">Jefe: {{ $modulo->jefeEnfermeria->nombre_completo }}</p>
                    <p class="text-sm text-gray-500">{{ $modulo->habitaciones->count() }} habitaciones</p>
                </div>
                @empty
                <p class="text-gray-500">No tienes módulos asignados</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal para completar procedimiento -->
<div id="modalCompletarProcedimiento" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Completar Procedimiento</h3>
            <form id="formCompletarProcedimiento" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Aplicación</label>
                    <input type="time" name="hora_aplicacion" value="{{ now()->format('H:i') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea name="observaciones_procedimiento" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Observaciones sobre el procedimiento..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios del Paciente</label>
                    <textarea name="comentarios_paciente" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Comentarios del paciente..."></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="cerrarModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Completar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function completarProcedimiento(procedimientoId) {
    document.getElementById('formCompletarProcedimiento').action = `/auxiliar-enfermeria/procedimientos/${procedimientoId}/completar`;
    document.getElementById('modalCompletarProcedimiento').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalCompletarProcedimiento').classList.add('hidden');
}
</script>
@endsection
