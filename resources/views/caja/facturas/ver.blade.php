@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
            <h1 class="text-2xl font-bold">Factura {{ $factura->numero_factura }}</h1>
            <span class="px-4 py-2 rounded-lg {{ $factura->estado == 'pendiente' ? 'bg-yellow-500' : 'bg-green-500' }}">
                {{ ucfirst($factura->estado) }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            <!-- Datos del Paciente -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Datos del Paciente</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Nombre:</span>
                        <p class="font-medium">{{ $factura->paciente->nombres }} {{ $factura->paciente->apellidos }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">DNI:</span>
                        <p class="font-medium">{{ $factura->paciente->dni }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Teléfono:</span>
                        <p class="font-medium">{{ $factura->paciente->telefono ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Email:</span>
                        <p class="font-medium">{{ $factura->paciente->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Detalles de Consulta -->
            @if($factura->consulta)
            <div class="bg-blue-50 rounded-lg p-4">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Detalles de la Consulta</h2>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-gray-600">Motivo:</span>
                        <p class="font-medium">{{ $factura->consulta->motivo_consulta }}</p>
                    </div>
                    @if($factura->consulta->diagnostico)
                    <div>
                        <span class="text-sm text-gray-600">Diagnóstico:</span>
                        <p class="font-medium">{{ $factura->consulta->diagnostico }}</p>
                    </div>
                    @endif
                    @if($factura->consulta->tratamiento)
                    <div>
                        <span class="text-sm text-gray-600">Tratamiento:</span>
                        <p class="font-medium">{{ $factura->consulta->tratamiento }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Detalles de Pago -->
            <div class="border-t pt-4">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Detalles de Pago</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">${{ number_format($factura->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Impuestos:</span>
                        <span class="font-medium">${{ number_format($factura->impuestos, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold border-t pt-2">
                        <span>Total:</span>
                        <span class="text-blue-600">${{ number_format($factura->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Información de Pago</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Fecha Emisión:</span>
                        <p class="font-medium">{{ $factura->fecha_emision->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($factura->fecha_pago)
                    <div>
                        <span class="text-sm text-gray-600">Fecha Pago:</span>
                        <p class="font-medium">{{ $factura->fecha_pago->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-sm text-gray-600">Método de Pago:</span>
                        <p class="font-medium">{{ ucfirst($factura->metodo_pago) }}</p>
                    </div>
                    @if($factura->monto_recibido)
                    <div>
                        <span class="text-sm text-gray-600">Monto Recibido:</span>
                        <p class="font-medium">${{ number_format($factura->monto_recibido, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex gap-4 pt-4 border-t">
                @if($factura->estado == 'pendiente')
                <a href="{{ route('caja.facturas.procesar-pago', $factura->id) }}" 
                   class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition">
                    Procesar Pago
                </a>
                @endif
                <a href="{{ route('caja.dashboard') }}" 
                   class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition">
                    Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

