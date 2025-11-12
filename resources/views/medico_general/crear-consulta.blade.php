@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('medico_general.pacientes.show', $paciente) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver al paciente
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nueva consulta</h1>
                <p class="text-sm text-gray-500">Paciente: {{ $paciente->nombre_completo }} ({{ $paciente->dni }})</p>
            </div>
            {{-- TODO: permitir seleccionar tipo de consulta (presencial, virtual, etc.) --}}
        </div>

        <form method="POST" action="{{ route('medico_general.pacientes.store-consulta', $paciente) }}" class="px-6 py-6 space-y-6">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="motivo_consulta">Motivo de la consulta</label>
                <textarea id="motivo_consulta" name="motivo_consulta" rows="4" required
                          class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                          placeholder="Describe brevemente el motivo..."></textarea>
                @error('motivo_consulta')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="fecha_consulta">Fecha</label>
                    <input id="fecha_consulta" type="date" name="fecha_consulta" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('fecha_consulta')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="hora_consulta">Hora</label>
                    <input id="hora_consulta" type="time" name="hora_consulta" value="{{ now()->format('H:i') }}" required
                           class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('hora_consulta')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="diagnostico">Diagnóstico preliminar</label>
                    <textarea id="diagnostico" name="diagnostico" rows="3" required
                              class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                              placeholder="Resultado preliminar de la evaluación..."></textarea>
                    @error('diagnostico')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="tratamiento">Tratamiento sugerido</label>
                    <textarea id="tratamiento" name="tratamiento" rows="3" required
                              class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                              placeholder="Medicamentos, dosis, recomendaciones..."></textarea>
                    @error('tratamiento')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="observaciones">Observaciones adicionales</label>
                <textarea id="observaciones" name="observaciones" rows="3"
                          class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                          placeholder="Notas internas, indicaciones para enfermería, etc."></textarea>
            </div>

            <div class="flex justify-end space-x-3 border-t pt-4">
                <a href="{{ route('medico_general.pacientes.show', $paciente) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Guardar consulta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
