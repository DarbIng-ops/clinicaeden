@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('medico_especialista.pacientes.show', $paciente) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver al paciente
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Consulta especializada</h1>
                <p class="text-sm text-gray-500">Paciente: {{ $paciente->nombre_completo }} · {{ $paciente->dni }}</p>
            </div>
            {{-- TODO: mostrar especialidad del médico autenticado --}}
        </div>

        <form method="POST" action="{{ route('medico_especialista.pacientes.store-consulta', $paciente) }}" class="px-6 py-6 space-y-6">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="especialidad">Especialidad</label>
                    <input id="especialidad" type="text" name="especialidad" value="{{ auth()->user()->especialidad ?? '' }}" required
                           class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                           placeholder="Ej. Cardiología, Neurología">
                    @error('especialidad')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="fecha_consulta">Fecha</label>
                    <input id="fecha_consulta" type="date" name="fecha_consulta" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    @error('fecha_consulta')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="hora_consulta">Hora</label>
                    <input id="hora_consulta" type="time" name="hora_consulta" value="{{ now()->format('H:i') }}" required
                           class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    @error('hora_consulta')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="motivo_consulta">Motivo</label>
                    <input id="motivo_consulta" type="text" name="motivo_consulta" required
                           class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                           placeholder="Motivo principal de la interconsulta">
                    @error('motivo_consulta')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="diagnostico">Diagnóstico especializado</label>
                <textarea id="diagnostico" name="diagnostico" rows="4" required
                          class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                          placeholder="Detalles del diagnóstico, hallazgos clínicos relevantes..."></textarea>
                @error('diagnostico')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="tratamiento">Plan terapéutico / Recomendaciones</label>
                <textarea id="tratamiento" name="tratamiento" rows="3" required
                          class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                          placeholder="Tratamientos sugeridos, derivaciones, seguimiento..."></textarea>
                @error('tratamiento')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="observaciones">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="3"
                          class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                          placeholder="Notas internas o instrucciones para otros profesionales"></textarea>
            </div>

            <div class="flex justify-end gap-3 border-t pt-4">
                <a href="{{ route('medico_especialista.pacientes.show', $paciente) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i>Registrar consulta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
