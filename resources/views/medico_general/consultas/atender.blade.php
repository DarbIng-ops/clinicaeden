@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
            <h1 class="text-2xl font-bold">Atención Médica</h1>
        </div>

        <form method="POST" action="{{ route('medico_general.consultas.finalizar', $consulta->id) }}" class="p-6">
            @csrf

            <div class="mb-8 bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Datos del Paciente
                </h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Nombre Completo:</span>
                        <p class="font-medium text-lg">{{ $consulta->paciente->nombres }} {{ $consulta->paciente->apellidos }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">DNI:</span>
                        <p class="font-medium text-lg">{{ $consulta->paciente->dni }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Edad:</span>
                        <p class="font-medium text-lg">{{ $consulta->paciente->edad ?? 'N/A' }} años</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Sexo:</span>
                        <p class="font-medium">{{ $consulta->paciente->sexo == 'M' ? 'Masculino' : 'Femenino' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Teléfono:</span>
                        <p class="font-medium">{{ $consulta->paciente->telefono ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Tipo de Sangre:</span>
                        <p class="font-medium">{{ $consulta->paciente->tipo_sangre ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-600">Motivo de Consulta:</span>
                    <p class="font-medium text-blue-700 bg-blue-50 p-3 rounded mt-1">{{ $consulta->motivo_consulta }}</p>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Signos Vitales
                </h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Presión Arterial</label>
                        <input type="text" name="presion_arterial" placeholder="120/80" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Temperatura (°C)</label>
                        <input type="number" step="0.1" name="temperatura" placeholder="36.5" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frecuencia Cardíaca (lpm)</label>
                        <input type="number" name="frecuencia_cardiaca" placeholder="70" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frecuencia Respiratoria (rpm)</label>
                        <input type="number" name="frecuencia_respiratoria" placeholder="16" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Saturación O2 (%)</label>
                        <input type="number" name="saturacion_oxigeno" placeholder="98" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                        <input type="number" step="0.1" name="peso" placeholder="70.5" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Talla (cm)</label>
                        <input type="number" step="0.1" name="talla" placeholder="170" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Evaluación Médica</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Diagnóstico <span class="text-red-500">*</span>
                        </label>
                        <textarea name="diagnostico" rows="4" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                                  placeholder="Describa el diagnóstico..."></textarea>
                        @error('diagnostico')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tratamiento / Medicación <span class="text-red-500">*</span>
                        </label>
                        <textarea name="tratamiento" rows="4" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                                  placeholder="Describa el tratamiento, medicamentos y dosis..."></textarea>
                        @error('tratamiento')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-8 bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Derivación del Paciente</h2>
                <p class="text-sm text-gray-600 mb-4">Seleccione qué hacer con el paciente después de la consulta:</p>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="accion" value="caja" required class="w-5 h-5 text-blue-600">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Derivar a Caja</span>
                            <p class="text-sm text-gray-600">El paciente debe pasar por caja para realizar el pago. Luego recepción procesará la salida.</p>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="accion" value="hospitalizar" required class="w-5 h-5 text-red-600">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Hospitalizar</span>
                            <p class="text-sm text-gray-600">El paciente requiere internación y será trasladado al área de hospitalización.</p>
                        </div>
                    </label>
                </div>
                @error('accion')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4 pt-4 border-t">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    Finalizar Consulta
                </button>
                <a href="{{ route('medico_general.dashboard') }}" 
                   class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
