@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Hospitalización #{{ $hospitalizacion->id }}</h2>
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800">← Volver</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Información del paciente --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Paciente</h3>
                <p><span class="font-medium">Nombre:</span> {{ $hospitalizacion->paciente?->nombre_completo ?? '—' }}</p>
                <p><span class="font-medium">DNI:</span> {{ $hospitalizacion->paciente?->dni ?? '—' }}</p>
            </div>

            {{-- Información de la hospitalización --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Datos de Internación</h3>
                <p><span class="font-medium">Habitación:</span> {{ $hospitalizacion->habitacion?->numero ?? '—' }}</p>
                <p><span class="font-medium">Estado:</span>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        {{ ucfirst(str_replace('_', ' ', $hospitalizacion->estado)) }}
                    </span>
                </p>
                <p><span class="font-medium">Ingreso:</span> {{ $hospitalizacion->fecha_ingreso?->format('d/m/Y H:i') ?? '—' }}</p>
                @if($hospitalizacion->fecha_egreso)
                    <p><span class="font-medium">Egreso:</span> {{ $hospitalizacion->fecha_egreso->format('d/m/Y H:i') }}</p>
                @endif
            </div>

            {{-- Motivo --}}
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Motivo de Hospitalización</h3>
                <p class="text-gray-600">{{ $hospitalizacion->motivo_hospitalizacion }}</p>
            </div>

            @if($hospitalizacion->observaciones)
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Observaciones</h3>
                <p class="text-gray-600">{{ $hospitalizacion->observaciones }}</p>
            </div>
            @endif

            {{-- Personal asignado --}}
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Personal Asignado</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Médico General</p>
                        <p class="font-medium">{{ $hospitalizacion->medicoGeneral?->name ?? 'No asignado' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jefe Enfermería</p>
                        <p class="font-medium">{{ $hospitalizacion->jefeEnfermeria?->name ?? 'No asignado' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Auxiliar Enfermería</p>
                        <p class="font-medium">{{ $hospitalizacion->auxiliarEnfermeria?->name ?? 'No asignado' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones según estado --}}
        @if(in_array(auth()->user()->role, ['admin', 'medico_general']))
        <div class="mt-6 pt-6 border-t border-gray-200 flex space-x-3">
            @if($hospitalizacion->estado === 'activo')
            <form method="POST" action="{{ route('hospitalizaciones.alta-medica', $hospitalizacion) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">
                    Dar Alta Médica
                </button>
            </form>
            @endif
            @if($hospitalizacion->estado === 'alta_enfermeria')
            <form method="POST" action="{{ route('hospitalizaciones.completar-alta', $hospitalizacion) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    Completar Alta
                </button>
            </form>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
