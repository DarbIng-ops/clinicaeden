@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="text-white px-6 py-4 rounded-t-lg" style="background:#1A2E4A">
            <h1 class="text-2xl font-bold">Procesar Salida del Paciente</h1>
        </div>

        <div class="p-6 space-y-6">
            <!-- Datos del Paciente -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Datos del Paciente</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Nombre:</span>
                        <p class="font-medium text-lg">{{ $paciente->nombres }} {{ $paciente->apellidos }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">DNI:</span>
                        <p class="font-medium text-lg">{{ $paciente->dni }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Teléfono:</span>
                        <p class="font-medium">{{ $paciente->telefono ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Email:</span>
                        <p class="font-medium">{{ $paciente->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="rounded-lg p-4 border" style="background:#EBF3FA; border-color:#A8C8E8">
                <h2 class="text-lg font-bold mb-3 flex items-center gap-2" style="color:#1A2E4A">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#2D5F8A">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Pago Confirmado
                </h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Factura:</span>
                        <p class="font-medium">{{ $factura->numero_factura }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Monto Pagado:</span>
                        <p class="font-semibold" style="color:#2D5F8A">${{ number_format($factura->total, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Método:</span>
                        <p class="font-medium">{{ ucfirst($factura->metodo_pago) }}</p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('recepcion.confirmar-salida', $paciente->id) }}">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones de Salida (Opcional)
                    </label>
                    <textarea name="observaciones_salida" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"
                              placeholder="Ej: Se entregaron recetas, paciente en buen estado..."></textarea>
                </div>

                <div class="flex gap-4 pt-4 border-t">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        ✓ Confirmar Salida
                    </button>
                    <a href="{{ route('recepcion.salidas') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

