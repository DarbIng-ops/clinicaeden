@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('medico_general.hospitalizaciones.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a mis hospitalizaciones
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Hospitalizar paciente</h1>
            <p class="text-sm text-gray-500">Completa la información para registrar una nueva hospitalización.</p>
        </div>

        {{-- TODO: Actualizar acción del formulario cuando se defina la ruta de almacenamiento --}}
        <form method="POST" action="{{ route('medico_general.hospitalizaciones.store') }}" class="px-6 py-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="paciente_id" class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                    <select id="paciente_id" name="paciente_id" required
                            class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Selecciona un paciente</option>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}">{{ $paciente->nombre_completo }} · {{ $paciente->dni }}</option>
                        @endforeach
                    </select>
                    @error('paciente_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    {{-- TODO: habilitar búsqueda rápida de pacientes --}}
                </div>

                <div>
                    <label for="habitacion_id" class="block text-sm font-medium text-gray-700 mb-1">Habitación disponible</label>
                    <select id="habitacion_id" name="habitacion_id" required
                            class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Selecciona una habitación</option>
                        @foreach($habitaciones as $habitacion)
                            <option value="{{ $habitacion->id }}">
                                Hab. {{ $habitacion->numero }} · {{ $habitacion->modulo->nombre ?? 'Módulo N/D' }} · Piso {{ $habitacion->modulo->piso->numero ?? '?' }}
                            </option>
                        @endforeach
                    </select>
                    @error('habitacion_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Fecha ingreso</label>
                    <input id="fecha_ingreso" type="date" name="fecha_ingreso" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('fecha_ingreso')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="hora_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Hora ingreso</label>
                    <input id="hora_ingreso" type="time" name="hora_ingreso" value="{{ now()->format('H:i') }}" required
                           class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('hora_ingreso')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="diagnostico_preliminar" class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico preliminar</label>
                <textarea id="diagnostico_preliminar" name="diagnostico_preliminar" rows="4" required
                          class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                          placeholder="Describe el diagnóstico inicial, antecedentes relevantes y motivo de internación..."></textarea>
                @error('diagnostico_preliminar')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="indicaciones_iniciales" class="block text-sm font-medium text-gray-700 mb-1">Indicaciones iniciales</label>
                <textarea id="indicaciones_iniciales" name="indicaciones_iniciales" rows="3"
                          class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                          placeholder="Tratamientos, observaciones para enfermería, signos a monitorear..."></textarea>
                {{-- TODO: evaluar agregar adjuntos (p.e. órdenes médicas) --}}
            </div>

            <div class="flex justify-end space-x-3 border-t pt-4">
                <a href="{{ route('medico_general.hospitalizaciones.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    <i class="fas fa-bed mr-2"></i>Confirmar hospitalización
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
