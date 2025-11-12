@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg">
            <h1 class="text-2xl font-bold">Procesar Pago - Factura {{ $factura->numero_factura }}</h1>
        </div>

        <div class="p-6">
            <!-- Datos del Paciente -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
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
                </div>
            </div>

            <!-- Monto a Pagar -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                <div class="text-center">
                    <span class="text-gray-600 text-lg">Monto Total a Pagar:</span>
                    <p class="text-4xl font-bold text-blue-600 mt-2">${{ number_format($factura->total, 2) }}</p>
                </div>
            </div>

            <!-- Formulario de Pago -->
            <form method="POST" action="{{ route('caja.facturas.confirmar-pago', $factura->id) }}" class="space-y-6">
                @csrf

                <!-- Método de Pago -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Método de Pago <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="metodo_pago" value="efectivo" required class="w-5 h-5 text-green-600">
                            <div class="ml-3">
                                <span class="font-medium text-gray-900">💵 Efectivo</span>
                                <p class="text-sm text-gray-600">Pago en efectivo</p>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="metodo_pago" value="tarjeta" required class="w-5 h-5 text-blue-600">
                            <div class="ml-3">
                                <span class="font-medium text-gray-900">💳 Tarjeta</span>
                                <p class="text-sm text-gray-600">Tarjeta de crédito o débito</p>
                            </div>
                        </label>
                    </div>
                    @error('metodo_pago')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Monto Recibido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Monto Recibido <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500">$</span>
                        <input type="number" 
                               name="monto_recibido" 
                               step="0.01" 
                               min="{{ $factura->total }}"
                               value="{{ $factura->total }}"
                               required
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-3 text-lg focus:ring-2 focus:ring-green-500"
                               oninput="calcularCambio()">
                    </div>
                    @error('monto_recibido')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cambio -->
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-medium">Cambio:</span>
                        <span id="cambio" class="text-2xl font-bold text-green-600">$0.00</span>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 pt-4 border-t">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        ✓ Confirmar Pago
                    </button>
                    <a href="{{ route('caja.facturas.ver', $factura->id) }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcularCambio() {
    const total = {{ $factura->total }};
    const recibido = parseFloat(document.querySelector('input[name="monto_recibido"]').value) || 0;
    const cambio = Math.max(0, recibido - total);
    document.getElementById('cambio').textContent = '$' + cambio.toFixed(2);
}
</script>
@endsection

