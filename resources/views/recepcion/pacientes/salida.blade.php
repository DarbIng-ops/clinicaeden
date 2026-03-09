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
                
                <!-- Encuesta de Satisfacción -->
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">

                    <div class="flex items-center gap-2 px-6 py-3 bg-gray-50 border-b border-gray-200">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#2D5F8A">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Encuesta de Satisfacción</h2>
                    </div>

                    <div class="px-6 pb-6 pt-4 space-y-4">
                    <p class="text-xs text-gray-500">Solicite al paciente que califique su experiencia</p>

                    <!-- Calidad de Atención Médica -->
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-600">
                            Calidad de Atención Médica <span class="text-red-400">*</span>
                        </label>
                        <div class="flex gap-1" id="atencion-medica">
                            @foreach(['1' => 'Muy Mala', '2' => 'Mala', '3' => 'Regular', '4' => 'Buena', '5' => 'Excelente'] as $val => $label)
                            <button type="button"
                                    onclick="setRating('atencion_medica', {{ $val }})"
                                    data-value="{{ $val }}"
                                    class="rating-btn flex-1 py-2 px-1 text-xs rounded border border-gray-200 bg-white text-gray-500 transition-all duration-150 hover:border-eden-azul-medio hover:text-eden-azul-medio">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="atencion_medica" id="atencion_medica_value" required>
                    </div>

                    <!-- Tiempo de Espera -->
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-600">
                            Tiempo de Espera <span class="text-red-400">*</span>
                        </label>
                        <div class="flex gap-1" id="tiempo-espera">
                            @foreach(['1' => 'Muy Largo', '2' => 'Largo', '3' => 'Aceptable', '4' => 'Rápido', '5' => 'Muy Rápido'] as $val => $label)
                            <button type="button"
                                    onclick="setRating('tiempo_espera', {{ $val }})"
                                    data-value="{{ $val }}"
                                    class="rating-btn flex-1 py-2 px-1 text-xs rounded border border-gray-200 bg-white text-gray-500 transition-all duration-150 hover:border-eden-azul-medio hover:text-eden-azul-medio">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="tiempo_espera" id="tiempo_espera_value" required>
                    </div>

                    <!-- Trato del Personal -->
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-600">
                            Trato del Personal <span class="text-red-400">*</span>
                        </label>
                        <div class="flex gap-1" id="trato-personal">
                            @foreach(['1' => 'Muy Malo', '2' => 'Malo', '3' => 'Regular', '4' => 'Bueno', '5' => 'Excelente'] as $val => $label)
                            <button type="button"
                                    onclick="setRating('trato_personal', {{ $val }})"
                                    data-value="{{ $val }}"
                                    class="rating-btn flex-1 py-2 px-1 text-xs rounded border border-gray-200 bg-white text-gray-500 transition-all duration-150 hover:border-eden-azul-medio hover:text-eden-azul-medio">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="trato_personal" id="trato_personal_value" required>
                    </div>

                    <!-- Comentarios Adicionales -->
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-600">Comentarios o Sugerencias <span class="text-gray-400">(Opcional)</span></label>
                        <textarea name="comentarios_encuesta" rows="3"
                                  class="w-full border border-gray-200 rounded px-3 py-2 text-sm text-gray-700 focus:ring-1 focus:ring-eden-azul-medio focus:border-eden-azul-medio outline-none"
                                  placeholder="El paciente puede compartir comentarios adicionales..."></textarea>
                    </div>

                    </div>{{-- /px-6 --}}
                </div>

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

<script>
function setRating(category, value) {
    const containerId = category.replace(/_/g, '-');

    // Restaurar todos los botones del grupo al estado inactivo
    document.querySelectorAll(`#${containerId} .rating-btn`).forEach(btn => {
        btn.classList.remove('bg-eden-azul-medio', 'text-white', 'border-eden-azul-medio', 'font-semibold');
        btn.classList.add('bg-white', 'text-gray-500', 'border-gray-200');
    });

    // Activar el botón seleccionado
    const selected = document.querySelector(`#${containerId} [data-value="${value}"]`);
    if (selected) {
        selected.classList.remove('bg-white', 'text-gray-500', 'border-gray-200');
        selected.classList.add('bg-eden-azul-medio', 'text-white', 'border-eden-azul-medio', 'font-semibold');
    }

    // Guardar valor en hidden input
    const input = document.getElementById(`${category}_value`);
    if (input) input.value = value;
}

// Validar antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const required = ['atencion_medica', 'tiempo_espera', 'trato_personal'];
    const missing = required.filter(f => !document.getElementById(`${f}_value`).value);

    if (missing.length) {
        e.preventDefault();
        alert('Por favor complete todas las calificaciones de la encuesta:\n- ' + missing.map(f => f.replace(/_/g, ' ')).join('\n- '));
    }
});
</script>
@endsection

