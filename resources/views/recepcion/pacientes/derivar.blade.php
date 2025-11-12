@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Derivar Paciente a Médico General</h1>
        
        <!-- Datos del Paciente -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Datos del Paciente</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-600">Nombre Completo:</span>
                    <p class="font-medium">{{ $paciente->nombres }} {{ $paciente->apellidos }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">DNI:</span>
                    <p class="font-medium">{{ $paciente->dni }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Edad:</span>
                    <p class="font-medium">{{ $paciente->edad ?? 'N/A' }} años</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Teléfono:</span>
                    <p class="font-medium">{{ $paciente->telefono ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Formulario de Derivación -->
        <form method="POST" action="{{ route('recepcion.consultas.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

            <!-- Seleccionar Médico -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Médico General <span class="text-red-500">*</span>
                </label>
                <select name="medico_id" required 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Seleccione un médico...</option>
                    @foreach($medicosGenerales as $medico)
                        <option value="{{ $medico->id }}">
                            Dr(a). {{ $medico->name }} {{ $medico->apellido }}
                        </option>
                    @endforeach
                </select>
                @error('medico_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Motivo de Consulta -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo de Consulta <span class="text-red-500">*</span>
                </label>
                <textarea name="motivo_consulta" rows="4" required
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Describa el motivo de la consulta..."></textarea>
                @error('motivo_consulta')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Observaciones Adicionales -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Observaciones Adicionales (Opcional)
                </label>
                <textarea name="observaciones" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Información adicional relevante..."></textarea>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    Derivar a Médico
                </button>
                <a href="{{ route('recepcion.dashboard') }}" 
                   class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-lg text-center transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
