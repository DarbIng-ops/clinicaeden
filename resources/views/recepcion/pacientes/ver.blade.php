@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Detalles del Paciente</h1>
        <div class="space-x-2">
            <a href="{{ route('recepcion.pacientes.edit', $paciente) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <a href="{{ route('recepcion.pacientes.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-6">
                    @if($paciente->foto)
                        <img src="{{ asset('storage/' . $paciente->foto) }}" alt="{{ $paciente->nombre_completo }}" class="h-20 w-20 rounded-full object-cover mr-4">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                            <i class="fas fa-user text-gray-600 text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $paciente->nombre_completo }}</h2>
                        <p class="text-gray-600">DNI: {{ $paciente->dni }}</p>
                        <p class="text-gray-600">Edad: {{ $paciente->edad }} años</p>
                    </div>
                </div>

                <!-- Información Personal -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2"></i>Información Personal
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">DNI</label>
                            <p class="text-sm text-gray-900">{{ $paciente->dni }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nombres Completos</label>
                            <p class="text-sm text-gray-900">{{ $paciente->nombre_completo }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Fecha de Nacimiento</label>
                            <p class="text-sm text-gray-900">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Sexo</label>
                            <p class="text-sm text-gray-900">
                                @switch($paciente->sexo)
                                    @case('M')
                                        Masculino
                                        @break
                                    @case('F')
                                        Femenino
                                        @break
                                    @default
                                        {{ $paciente->sexo }}
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Edad</label>
                            <p class="text-sm text-gray-900">{{ $paciente->edad }} años</p>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-phone mr-2"></i>Información de Contacto
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Teléfono</label>
                            <p class="text-sm text-gray-900">{{ $paciente->telefono }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="text-sm text-gray-900">{{ $paciente->email }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Dirección</label>
                            <p class="text-sm text-gray-900">{{ $paciente->direccion }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Ciudad</label>
                            <p class="text-sm text-gray-900">{{ $paciente->ciudad }}</p>
                        </div>
                    </div>
                </div>

                <!-- Información Médica -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-heartbeat mr-2"></i>Información Médica
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Tipo de Sangre</label>
                            <p class="text-sm text-gray-900">
                                @if($paciente->tipo_sangre)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $paciente->tipo_sangre }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No especificado</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Alergias</label>
                            <p class="text-sm text-gray-900">
                                @if($paciente->alergias)
                                    {{ $paciente->alergias }}
                                @else
                                    <span class="text-gray-400">Ninguna registrada</span>
                                @endif
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Enfermedades Crónicas</label>
                            <p class="text-sm text-gray-900">
                                @if($paciente->enfermedades_cronicas)
                                    {{ $paciente->enfermedades_cronicas }}
                                @else
                                    <span class="text-gray-400">Ninguna registrada</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contacto de Emergencia -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Contacto de Emergencia
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nombre del Contacto</label>
                            <p class="text-sm text-gray-900">{{ $paciente->contacto_emergencia_nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Teléfono del Contacto</label>
                            <p class="text-sm text-gray-900">{{ $paciente->contacto_emergencia_telefono }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="lg:col-span-1">
            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    <a href="{{ route('recepcion.pacientes.edit', $paciente) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>Editar Información
                    </a>
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-calendar-plus mr-2"></i>Nueva Cita
                    </button>
                    <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-file-medical mr-2"></i>Nueva Consulta
                    </button>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Sistema</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Estado</label>
                        <p class="text-sm text-gray-900">
                            @if($paciente->activo)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Inactivo
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Registrado</label>
                        <p class="text-sm text-gray-900">{{ $paciente->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Última Actualización</label>
                        <p class="text-sm text-gray-900">{{ $paciente->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Total de Citas</label>
                        <p class="text-sm text-gray-900">{{ $paciente->citas->count() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Total de Consultas</label>
                        <p class="text-sm text-gray-900">{{ $paciente->consultas->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Citas -->
    @if($paciente->citas->count() > 0)
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-calendar mr-2"></i>Historial de Citas
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($paciente->citas as $cita)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $cita->fecha->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $cita->hora }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $cita->medico->name ?? 'No asignado' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $cita->motivo_consulta }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($cita->estado == 'pendiente') bg-yellow-100 text-yellow-800
                                            @elseif($cita->estado == 'completada') bg-green-100 text-green-800
                                            @elseif($cita->estado == 'cancelada') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($cita->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection